(function () {
  function setupAdditionalCosts(prefix) {
    const addBtn = document.querySelector(
      `.js-add-cost-row[data-prefix="${prefix}"]`,
    );
    const container = document.getElementById(
      `${prefix}-additional-costs-container`,
    );
    const template = document.getElementById(
      `${prefix}-additional-cost-row-template`,
    );
    if (!container || !template) return;

    let indexCounter = container.querySelectorAll(`.${prefix}-cost-row`).length;

    if (addBtn) {
      addBtn.addEventListener("click", () => {
        try {
          if (container.querySelector("p.text-muted")) container.innerHTML = "";
          const html = template.innerHTML.replace(/__INDEX__/g, indexCounter++);
          const wrap = document.createElement("div");
          wrap.innerHTML = html.trim();
          container.appendChild(wrap.firstElementChild);
          // Update totals after adding a cost row
          try {
            window._prManager && window._prManager.updateTotals(prefix);
          } catch (_) {}
        } catch (e) {
          console.error("Gagal menambah baris biaya:", e);
        }
      });
    }

    container.addEventListener("click", (e) => {
      const btn = e.target.closest(".js-remove-cost-row");
      if (!btn) return;
      const row = btn.closest(`.${prefix}-cost-row`);
      if (row) row.remove();
      if (container && container.children.length === 0) {
        container.innerHTML =
          '<p class="text-muted mb-0">Belum ada biaya tambahan. Klik "Tambah Biaya".</p>';
        indexCounter = 0;
      }
      // Update totals after removing a cost row
      try {
        window._prManager && window._prManager.updateTotals(prefix);
      } catch (_) {}
    });

    // Recalculate totals when amount changes
    container.addEventListener("input", (e) => {
      const t = e.target;
      if (t && t.name && /additional_costs\[[^\]]+\]\[amount\]/.test(t.name)) {
        try {
          window._prManager && window._prManager.updateTotals(prefix);
        } catch (_) {}
      }
    });
  }

  class PurchaseReceiptManager {
    constructor() {
      this.quickReceiveModal = null;
    }
    // Format quantity as integer (no decimals)
    formatQty(n) {
      const num = Number.isNaN(Number(n)) ? 0 : Number(n);
      return String(Math.round(num));
    }

    // Currency formatter (IDR) with integer rounding
    formatCurrency(amount) {
      const a = Number.isNaN(Number(amount)) ? 0 : Math.round(Number(amount));
      try {
        return "Rp " + new Intl.NumberFormat("id-ID").format(a);
      } catch (_) {
        return "Rp " + String(a);
      }
    }

    // Clamp a number between bounds
    clamp(n, min, max) {
      const v = isNaN(n) ? 0 : n;
      return Math.min(max, Math.max(min, v));
    }

    // Compute per-item status based on received vs ordered
    computeItemStatus(received, ordered) {
      if (ordered <= 0) return "Diterima";
      if (received <= 0) return "Ditolak";
      if (received >= ordered) return "Diterima";
      return "Sebagian";
    }

    // Update a single row UI (rejected qty, item status, progress, validation)
    updateRowUI(row) {
      const input = row.querySelector(".received-input");
      if (!input) return;
      const unit = input.getAttribute("data-unit") || "";
      const ordered = Math.round(
        parseFloat(input.getAttribute("max")) ||
          parseFloat(input.getAttribute("data-ordered")) ||
          0,
      );
      let received = Math.round(parseFloat(input.value));
      if (isNaN(received)) received = 0;

      // Clamp and normalize value
      const clamped = Math.round(this.clamp(received, 0, ordered));
      if (clamped !== received) input.value = this.formatQty(clamped);
      received = clamped;

      // Validation state
      const invalid = received < 0 || received > ordered || isNaN(received);
      input.classList.toggle("is-invalid", invalid);

      // Rejected text
      const rejected = Math.max(0, ordered - received);
      const rejEl =
        row.querySelector(".js-rejected") ||
        row.querySelector(".form-text.text-muted");
      if (rejEl)
        rejEl.textContent = `Otomatis: ${this.formatQty(rejected)} ${unit}`;
      // Set hidden rejected
      const rejHidden = row.querySelector('input[name*="[rejected_quantity]"]');
      if (rejHidden) rejHidden.value = this.formatQty(rejected);

      // Item status text
      const status = this.computeItemStatus(received, ordered);
      const statusEl = row.querySelector(".js-item-status");
      if (statusEl) {
        statusEl.textContent = `Otomatis: ${status}`;
        statusEl.classList.remove(
          "text-success",
          "text-danger",
          "text-warning",
        );
        statusEl.classList.add(
          status === "Diterima"
            ? "text-success"
            : status === "Ditolak"
              ? "text-danger"
              : "text-warning",
        );
      }
      // Set hidden item_status (backend key)
      const toKey = (txt) =>
        txt === "Diterima"
          ? "accepted"
          : txt === "Ditolak"
            ? "rejected"
            : "partial";
      const statusHidden = row.querySelector('input[name*="[item_status]"]');
      if (statusHidden) statusHidden.value = toKey(status);

      // Progress bar
      const progEl = row.querySelector(".js-progress");
      const progText = row.querySelector(".js-progress-text");
      if (progEl) {
        const pct =
          ordered > 0
            ? Math.min(100, Math.round((received / ordered) * 100))
            : 0;
        progEl.style.width = `${pct}%`;
        progEl.classList.remove("bg-success", "bg-warning", "bg-danger");
        progEl.classList.add(
          status === "Diterima"
            ? "bg-success"
            : status === "Ditolak"
              ? "bg-danger"
              : "bg-warning",
        );
      }
      if (progText) {
        const pct =
          ordered > 0
            ? Math.min(100, Math.round((received / ordered) * 100))
            : 0;
        progText.textContent = `${this.formatQty(received)} / ${this.formatQty(ordered)} ${unit} (${pct}%)`;
      }
    }

    // Attach listeners for a single row
    attachRowHandlers(row) {
      const input = row.querySelector(".received-input");
      if (!input) return;

      // Enforce integer step/min attributes
      try {
        input.setAttribute("step", "1");
        if (!input.getAttribute("min")) input.setAttribute("min", "0");
      } catch (_) {}

      const onChange = () => {
        this.updateRowUI(row);
        // Update overall after each change
        this.updateOverallStatus(
          row.closest(
            ".card-body, #qr-items-container, #items-container, form",
          ),
        );
        // Update totals
        try {
          const prefix = row.closest("#qr-items-container") ? "qr" : "pr";
          this.updateTotals(prefix);
        } catch (_) {}
      };

      input.addEventListener("input", onChange);
      input.addEventListener("blur", onChange);

      const decBtn = row.querySelector(".btn-decrement");
      const incBtn = row.querySelector(".btn-increment");
      input.setAttribute("step", "1");
      const step = parseInt(input.getAttribute("step"), 10) || 1;
      const ordered = Math.round(parseFloat(input.getAttribute("max")) || 0);

      if (decBtn)
        decBtn.addEventListener("click", () => {
          const current = Math.round(parseFloat(input.value) || 0);
          const next = Math.round(this.clamp(current - step, 0, ordered));
          input.value = this.formatQty(next);
          onChange();
        });

      if (incBtn)
        incBtn.addEventListener("click", () => {
          const current = Math.round(parseFloat(input.value) || 0);
          const next = Math.round(this.clamp(current + step, 0, ordered));
          input.value = this.formatQty(next);
          onChange();
        });

      // Initial paint
      this.updateRowUI(row);
    }

    // Initialize handlers for all rows inside a container
    initializeItemRows(container) {
      if (!container) return;
      container
        .querySelectorAll(".pr-row")
        .forEach((row) => this.attachRowHandlers(row));
    }

    // Compute and update overall status text
    updateOverallStatus(scopeEl) {
      if (!scopeEl) return;
      const rows = scopeEl.querySelectorAll(".pr-row");
      if (!rows || rows.length === 0) return;
      let allAccepted = true;
      let allRejected = true;
      rows.forEach((row) => {
        const input = row.querySelector(".received-input");
        const ordered = Math.round(parseFloat(input?.getAttribute("max")) || 0);
        const received = Math.round(parseFloat(input?.value) || 0);
        const st = this.computeItemStatus(received, ordered);
        if (st !== "Diterima") allAccepted = false;
        if (st !== "Ditolak") allRejected = false;
      });
      const overall = allAccepted
        ? "Diterima"
        : allRejected
          ? "Ditolak"
          : "Sebagian";

      // Map display text to backend value
      const toKey = (txt) =>
        txt === "Diterima"
          ? "accepted"
          : txt === "Ditolak"
            ? "rejected"
            : "partial";
      const overallKey = toKey(overall);

      // Update any overall status auto text found
      ["qr-status-auto", "pr-status-auto"].forEach((id) => {
        const el = document.getElementById(id);
        if (el) {
          el.textContent = `Otomatis: ${overall}`;
          el.classList.remove("text-success", "text-danger", "text-warning");
          el.classList.add(
            overall === "Diterima"
              ? "text-success"
              : overall === "Ditolak"
                ? "text-danger"
                : "text-warning",
          );
        }
      });

      // Update hidden inputs
      try {
        const qrHidden = document.getElementById("qr-status");
        if (qrHidden) qrHidden.value = overallKey;
        // Traverse up to the nearest form and set its status hidden field if present
        const form =
          scopeEl.closest("form") ||
          document.querySelector('form[action*="purchase-receipts"]');
        const statusHidden = form
          ? form.querySelector('input[name="status"]')
          : null;
        if (statusHidden) statusHidden.value = overallKey;
      } catch (_) {}
    }

    // Attach form-level validation on submit
    hookFormValidation(form) {
      if (!form || form._prValidationHooked) return;
      form._prValidationHooked = true;

      // Provide Indonesian custom validity messages for key inputs
      try {
        const dateInp = form.querySelector('input[name="receipt_date"]');
        if (dateInp) {
          dateInp.addEventListener("invalid", function () {
            this.setCustomValidity("Tanggal penerimaan wajib diisi.");
          });
          dateInp.addEventListener("input", function () {
            this.setCustomValidity("");
          });
        }
        const photoInp = form.querySelector('input[name="receipt_photo"]');
        if (photoInp) {
          photoInp.addEventListener("invalid", function () {
            // Only show when required and empty
            if (
              this.hasAttribute("required") &&
              (!this.files || this.files.length === 0)
            ) {
              this.setCustomValidity("Foto bukti penerimaan wajib diunggah.");
            }
          });
          photoInp.addEventListener("input", function () {
            this.setCustomValidity("");
          });
        }
      } catch (_) {}

      form.addEventListener("submit", (e) => {
        const container =
          form.querySelector("#qr-items-container, #items-container") || form;
        const rows = container.querySelectorAll(".pr-row");

        // Validate items presence
        if (!rows || rows.length === 0) {
          e.preventDefault();
          e.stopPropagation();
          try {
            if (window.Swal) {
              Swal.fire({
                icon: "error",
                title: "Validasi Gagal",
                text: "Tidak ada item untuk diterima. Pilih pesanan terlebih dahulu.",
              });
            } else {
              alert("Validasi gagal: Tidak ada item untuk diterima.");
            }
          } catch (_) {}
          return;
        }

        // Validate receipt photo (required, type, size)
        const photoInp = form.querySelector('input[name="receipt_photo"]');
        if (photoInp) {
          const files = photoInp.files;
          const isRequired = photoInp.hasAttribute("required");
          const hasFile = files && files.length > 0;
          if (isRequired && !hasFile) {
            e.preventDefault();
            e.stopPropagation();
            try {
              if (window.Swal) {
                Swal.fire({
                  icon: "error",
                  title: "Validasi Gagal",
                  text: "Foto bukti penerimaan wajib diunggah.",
                });
              } else {
                alert("Foto bukti penerimaan wajib diunggah.");
              }
            } catch (_) {}
            photoInp.focus();
            return;
          }
          if (hasFile) {
            const file = files[0];
            const allowed = ["image/jpeg", "image/png", "image/jpg"];
            const maxSize = 2 * 1024 * 1024; // 2MB
            if (allowed.indexOf(file.type) === -1) {
              e.preventDefault();
              e.stopPropagation();
              try {
                if (window.Swal) {
                  Swal.fire({
                    icon: "error",
                    title: "Validasi Gagal",
                    text: "Format foto tidak valid. Gunakan JPG atau PNG.",
                  });
                } else {
                  alert("Format foto tidak valid. Gunakan JPG atau PNG.");
                }
              } catch (_) {}
              photoInp.value = "";
              photoInp.focus();
              return;
            }
            if (file.size > maxSize) {
              e.preventDefault();
              e.stopPropagation();
              try {
                if (window.Swal) {
                  Swal.fire({
                    icon: "error",
                    title: "Validasi Gagal",
                    text: "Ukuran foto melebihi 2MB.",
                  });
                } else {
                  alert("Ukuran foto melebihi 2MB.");
                }
              } catch (_) {}
              photoInp.value = "";
              photoInp.focus();
              return;
            }
          }
        }

        // Validate per-item condition photos: required when input has required attribute; also enforce type & size
        try {
          const condPhotos = form.querySelectorAll(
            'input[type="file"][name*="[condition_photo]"]',
          );
          // Check missing required first
          let missingRequired = null;
          condPhotos.forEach((inp) => {
            const isReq = inp.hasAttribute("required");
            const hasFile = inp.files && inp.files.length > 0;
            if (isReq && !hasFile && !missingRequired) {
              missingRequired = inp;
            }
          });
          if (missingRequired) {
            e.preventDefault();
            e.stopPropagation();
            try {
              if (window.Swal) {
                Swal.fire({
                  icon: "error",
                  title: "Validasi Gagal",
                  text: "Foto kondisi item wajib diunggah untuk setiap item yang belum memiliki foto.",
                });
              } else {
                alert(
                  "Foto kondisi item wajib diunggah untuk setiap item yang belum memiliki foto.",
                );
              }
            } catch (_) {}
            missingRequired.focus();
            return;
          }
          // Validate provided files (type & size)
          condPhotos.forEach((inp) => {
            const files = inp.files;
            if (files && files.length > 0) {
              const file = files[0];
              const allowed = ["image/jpeg", "image/png", "image/jpg"];
              const maxSize = 2 * 1024 * 1024; // 2MB
              if (allowed.indexOf(file.type) === -1) {
                e.preventDefault();
                e.stopPropagation();
                try {
                  if (window.Swal) {
                    Swal.fire({
                      icon: "error",
                      title: "Validasi Gagal",
                      text: "Format foto kondisi item tidak valid. Gunakan JPG atau PNG.",
                    });
                  } else {
                    alert(
                      "Format foto kondisi item tidak valid. Gunakan JPG atau PNG.",
                    );
                  }
                } catch (_) {}
                inp.value = "";
                throw new Error("Invalid condition photo type");
              }
              if (file.size > maxSize) {
                e.preventDefault();
                e.stopPropagation();
                try {
                  if (window.Swal) {
                    Swal.fire({
                      icon: "error",
                      title: "Validasi Gagal",
                      text: "Ukuran foto kondisi item melebihi 2MB.",
                    });
                  } else {
                    alert("Ukuran foto kondisi item melebihi 2MB.");
                  }
                } catch (_) {}
                inp.value = "";
                throw new Error("Invalid condition photo size");
              }
            }
          });
        } catch (_) {}

        let firstInvalid = null;
        rows.forEach((row) => {
          this.updateRowUI(row);
          const input = row.querySelector(".received-input");
          if (input && input.classList.contains("is-invalid") && !firstInvalid)
            firstInvalid = input;
        });
        if (firstInvalid) {
          e.preventDefault();
          e.stopPropagation();
          try {
            if (window.Swal) {
              Swal.fire({
                icon: "error",
                title: "Validasi Gagal",
                text: "Pastikan jumlah diterima berada dalam rentang yang valid untuk semua item.",
              });
            } else {
              alert("Validasi gagal: periksa jumlah diterima pada semua item.");
            }
          } catch (_) {}
          firstInvalid.focus();
        }
      });
    }

    getQuickReceiveModal() {
      if (this.quickReceiveModal) return this.quickReceiveModal;
      const modalEl = document.getElementById("quickReceiveModal");
      if (!modalEl || !window.bootstrap) return null;
      // Move modal to body to avoid z-index/stacking-context issues that can block clicks
      try {
        if (modalEl.parentElement !== document.body) {
          document.body.appendChild(modalEl);
        }
      } catch (e) {
        console.warn("Failed to reparent modal to body:", e);
      }
      // Initialize Bootstrap modal instance
      this.quickReceiveModal = new bootstrap.Modal(modalEl, {
        backdrop: true,
        keyboard: true,
      });
      this.bindQuickReceiveModalEvents(modalEl);
      return this.quickReceiveModal;
    }

    bindQuickReceiveModalEvents(modalEl) {
      modalEl.addEventListener("hidden.bs.modal", () => {
        const form = document.getElementById("quickReceiveForm");
        if (form) form.reset();
        const itemsContainer = document.getElementById("qr-items-container");
        if (itemsContainer)
          itemsContainer.innerHTML =
            '<p class="text-muted mb-0">Pilih PO untuk memuat item...</p>';
        this.resetQRAdditionalCosts();
        this.cleanupBackdrops();
      });
    }

    cleanupBackdrops() {
      try {
        document
          .querySelectorAll(".modal-backdrop")
          .forEach((el) => el.remove());
        document.body.classList.remove("modal-open");
        document.body.style.removeProperty("padding-right");
      } catch (e) {
        console.warn("Backdrop cleanup error:", e);
      }
    }

    resetQRAdditionalCosts() {
      const container = document.getElementById(
        "qr-additional-costs-container",
      );
      if (container) {
        container.innerHTML =
          '<p class="text-muted mb-0">Belum ada biaya tambahan. Klik "Tambah Biaya".</p>';
      }
    }

    openQuickReceive(btn) {
      try {
        const poId = btn.getAttribute("data-po-id");
        const poNumber = btn.getAttribute("data-po-number");
        const supplierName = btn.getAttribute("data-supplier");

        const poInput = document.getElementById("qr-purchase-order-id");
        if (poInput) poInput.value = poId;
        const poNum = document.getElementById("qr-po-number");
        if (poNum) poNum.textContent = poNumber;
        const suppName = document.getElementById("qr-supplier-name");
        if (suppName) suppName.textContent = supplierName;

        const today = new Date().toISOString().slice(0, 10);
        const rd = document.getElementById("qr-receipt-date");
        if (rd) rd.value = today;
        const st = document.getElementById("qr-status");
        if (st) st.value = "";

        const itemsContainer = document.getElementById("qr-items-container");
        if (itemsContainer)
          itemsContainer.innerHTML =
            '<div class="text-center py-3 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div>Memuat item...</div>';

        // Reset additional costs UI
        this.resetQRAdditionalCosts();

        // Reset discount & tax inputs in modal
        try {
          const disc = document.querySelector('input[name="discount_amount"]');
          const tax = document.querySelector('input[name="tax_amount"]');
          if (disc) disc.value = "";
          if (tax) tax.value = "";
        } catch (_) {}

        // Show modal (ensure clean state and correct stacking)
        this.cleanupBackdrops();
        const modal = this.getQuickReceiveModal();
        if (modal) {
          modal.show();
        }

        // Fetch and render items
        this.fetchItemsByPurchaseOrder(poId)
          .then((data) => this.renderQuickReceiveItems(itemsContainer, data))
          .catch((err) => {
            console.error("Gagal memuat item PO:", err);
            if (itemsContainer)
              itemsContainer.innerHTML =
                '<p class="text-danger text-center py-3">Gagal memuat item pesanan</p>';
          });
      } catch (e) {
        console.error(e);
      }
    }

    fetchItemsByPurchaseOrder(orderId) {
      return fetch(
        `/api/purchase-receipts/items-by-purchase-order?order_id=${orderId}`,
      ).then((resp) => resp.json());
    }

    renderQuickReceiveItems(itemsContainer, data) {
      let html = "";
      if (data.items && data.items.length) {
        data.items.forEach((item, index) => {
          const unitLabel =
            item.raw_material &&
            item.raw_material.unit &&
            item.raw_material.unit.name
              ? item.raw_material.unit.name
              : item.unit_name || "";
          const orderedQty = Math.round(
            typeof item.quantity === "number"
              ? item.quantity
              : parseFloat(item.quantity || 0),
          );
          const price = Math.round(
            typeof item.unit_price === "number"
              ? item.unit_price
              : parseFloat(item.unit_price || 0),
          );
          html += `
        <div class="flex flex-col mb-4 p-4 border rounded-lg shadow-md pr-row" data-price="${price}">
          <input type="hidden" name="items[${index}][purchase_order_item_id]" value="${item.id}">

          <div class="mb-2">
            <label class="block text-sm font-medium">Bahan</label>
            <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="${item.raw_material.name}" readonly>
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium">Dipesan</label>
            <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="${orderedQty} ${unitLabel}" readonly>
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium">Diterima <span class="text-red-500">*</span></label>
            <div class="flex items-center">
              <button class="btn btn-outline-secondary btn-decrement" type="button" aria-label="Kurangi">
                <i class="bi bi-dash"></i>
              </button>
              <input type="number" name="items[${index}][received_quantity]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm received-input" step="1" min="0" max="${orderedQty}" data-ordered="${orderedQty}" data-unit="${unitLabel}" value="${orderedQty}" required>
              <button class="btn btn-outline-secondary btn-increment" type="button" aria-label="Tambah">
                <i class="bi bi-plus"></i>
              </button>
              <div class="invalid-feedback">Nilai diterima harus 0 - ${orderedQty} ${unitLabel}</div>
            </div>
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium">Ditolak</label>
            <div class="form-text text-muted js-rejected">Otomatis: 0 ${unitLabel}</div>
            <input type="hidden" name="items[${index}][rejected_quantity]" value="">
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium">Status Item</label>
            <div class="form-text js-item-status text-green-500">Otomatis: Diterima</div>
            <input type="hidden" name="items[${index}][item_status]" value="">
          </div>
          <div class="mb-2">
            <div class="progress" style="height: 6px;">
              <div class="progress-bar js-progress bg-green-500" role="progressbar" style="width: 100%"></div>
            </div>
            <small class="text-muted js-progress-text">${orderedQty} / ${orderedQty} ${unitLabel} (100%)</small>
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium">Foto Kondisi Item <span class="text-red-500">*</span></label>
            <input type="file" name="items[${index}][condition_photo]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" accept="image/jpeg,image/png,image/jpg" required>
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium">Catatan Item</label>
            <textarea name="items[${index}][notes]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" rows="2" placeholder="Catatan untuk item ini..."></textarea>
          </div>
        </div>`;
        });
      } else {
        html =
          '<p class="text-muted text-center py-3">Tidak ada item ditemukan</p>';
      }
      if (itemsContainer) {
        itemsContainer.innerHTML = html;
        this.initializeItemRows(itemsContainer);
        this.updateOverallStatus(itemsContainer);
        const form = document.getElementById("quickReceiveForm");
        this.hookFormValidation(form);
        // Initial totals
        this.updateTotals("qr");
      }
    }

    setupCreateView() {
      const purchaseOrderSelect = document.querySelector(
        'select[name="purchase_order_id"]',
      );
      const itemsContainer = document.getElementById("items-container");
      if (!purchaseOrderSelect || !itemsContainer) return;

      // Hide status field since it's now automatically determined
      const statusField = document.querySelector(
        '.col-md-6 select[name="status"]',
      );
      if (statusField) {
        const statusCol = statusField.closest(".col-md-6");
        if (statusCol) {
          const label = statusCol.querySelector("label");
          if (label) label.textContent = "Status Penerimaan";
          statusField.style.display = "none";
          const autoText = document.createElement("div");
          autoText.className = "form-text text-muted";
          autoText.textContent = "Otomatis: Ditentukan dari status item";
          statusCol.appendChild(autoText);
        }
      }

      const renderItems = (orderId) => {
        if (!orderId) {
          itemsContainer.innerHTML =
            '<p class="text-muted text-center py-3">Pilih pesanan untuk menampilkan item</p>';
          return;
        }
        this.fetchItemsByPurchaseOrder(orderId)
          .then((data) => this.renderCreateItems(itemsContainer, data))
          .catch((error) => {
            console.error("Error loading items:", error);
            itemsContainer.innerHTML =
              '<p class="text-danger text-center py-3">Gagal memuat item pesanan</p>';
          });
      };

      purchaseOrderSelect.addEventListener("change", function () {
        renderItems(this.value);
      });

      // Initial state
      itemsContainer.innerHTML =
        '<p class="text-muted text-center py-3">Pilih pesanan untuk menampilkan item</p>';

      // Preselect based on query param if provided
      const params = new URLSearchParams(window.location.search);
      const preselectId = params.get("purchase_order_id");
      if (preselectId) {
        const option = Array.from(purchaseOrderSelect.options).find(
          (opt) => opt.value === preselectId,
        );
        if (option) {
          purchaseOrderSelect.value = preselectId;
          renderItems(preselectId);
        }
      } else if (purchaseOrderSelect.value) {
        // If old value exists (e.g., validation error), reload items
        renderItems(purchaseOrderSelect.value);
      }
    }

    renderCreateItems(itemsContainer, data) {
      let html = "";
      if (data.items && data.items.length > 0) {
        data.items.forEach((item, index) => {
          const unitLabel =
            item.raw_material &&
            item.raw_material.unit &&
            item.raw_material.unit.name
              ? item.raw_material.unit.name
              : item.unit_name || "";
          const qty = Math.round(
            typeof item.quantity === "number"
              ? item.quantity
              : parseFloat(item.quantity || 0),
          );
          const price = Math.round(
            typeof item.unit_price === "number"
              ? item.unit_price
              : parseFloat(item.unit_price || 0),
          );
          html += `
            <div class="row mb-3 p-3 border rounded pr-row" data-price="${price}">
              <input type="hidden" name="items[${index}][purchase_order_item_id]" value="${item.id}">

              <div class="col-md-3">
                <label class="form-label">Bahan</label>
                <input type="text" class="form-control" value="${item.raw_material.name}" readonly>
              </div>
              <div class="col-md-2">
                <label class="form-label">Dipesan</label>
                <input type="text" class="form-control" value="${qty} ${unitLabel}" readonly>
              </div>
              <div class="col-md-3">
                <label class="form-label">Diterima <span class="text-danger">*</span></label>
                <div class="input-group">
                  <button class="btn btn-outline-secondary btn-decrement" type="button" aria-label="Kurangi">
                    <i class="bi bi-dash"></i>
                  </button>
                  <input type="number" name="items[${index}][received_quantity]" class="form-control received-input" step="1" min="0" max="${qty}" data-ordered="${qty}" data-unit="${unitLabel}" value="${qty}" required>
                  <button class="btn btn-outline-secondary btn-increment" type="button" aria-label="Tambah">
                    <i class="bi bi-plus"></i>
                  </button>
                  <div class="invalid-feedback">Nilai diterima harus 0 - ${qty} ${unitLabel}</div>
                </div>
              </div>
              <div class="col-md-2">
                <label class="form-label">Ditolak</label>
                <div class="form-text text-muted js-rejected">Otomatis: 0 ${unitLabel}</div>
                <input type="hidden" name="items[${index}][rejected_quantity]" value="">
              </div>
              <div class="col-md-2">
                <label class="form-label">Status Item</label>
                <div class="form-text js-item-status text-success">Otomatis: Diterima</div>
                <input type="hidden" name="items[${index}][item_status]" value="">
              </div>
              <div class="col-12 mt-2">
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar js-progress bg-success" role="progressbar" style="width: 100%"></div>
                </div>
                <small class="text-muted js-progress-text">${qty} / ${qty} ${unitLabel} (100%)</small>
              </div>
              <div class="col-12 mt-2">
                <label class="form-label">Foto Kondisi Item <span class="text-danger">*</span></label>
                <input type="file" name="items[${index}][condition_photo]" class="form-control" accept="image/jpeg,image/png,image/jpg" required>
              </div>
              <div class="col-12 mt-2">
                <label class="form-label">Catatan Item</label>
                <textarea name="items[${index}][notes]" class="form-control" rows="2" placeholder="Catatan khusus untuk item ini..."></textarea>
              </div>
            </div>`;
        });
      } else {
        html =
          '<p class="text-muted text-center py-3">Tidak ada item ditemukan</p>';
      }
      itemsContainer.innerHTML = html;
      this.initializeItemRows(itemsContainer);
      this.updateOverallStatus(itemsContainer);
      const form = itemsContainer.closest("form");
      this.hookFormValidation(form);
      // Initial totals
      this.updateTotals("pr");
    }

    setupEditView() {
      // Edit page rows rendered server-side
      const form =
        document.querySelector(
          'form[action*="purchase-receipts"][method="post"]',
        ) || document.querySelector("form");
      if (!form) return;
      const rows = form.querySelectorAll(
        ".card-body .row.mb-3.p-3.border.rounded",
      );
      if (rows.length === 0) return;
      // Enhance rows with new logic
      rows.forEach((row) => {
        row.classList.add("pr-row");
        const rec = row.querySelector('input[name*="[received_quantity]"]');
        const rejectedText = row.querySelector(".form-text.text-muted");
        if (!rec || !rejectedText) return;
        const unitText = rejectedText.textContent.split(" ").slice(-1)[0];
        rec.classList.add("received-input");
        rec.setAttribute("data-unit", unitText);
        // Ensure integer step/min/max
        rec.setAttribute("step", "1");
        if (!rec.getAttribute("min")) rec.setAttribute("min", "0");
        const maxAttr = rec.getAttribute("max");
        if (maxAttr)
          rec.setAttribute("max", String(Math.round(parseFloat(maxAttr) || 0)));
        // Sanitize current value
        rec.value = this.formatQty(rec.value);
        // Add invalid feedback if missing
        if (!row.querySelector(".invalid-feedback")) {
          const fb = document.createElement("div");
          fb.className = "invalid-feedback";
          const max = rec.getAttribute("max") || "";
          fb.textContent = `Nilai diterima harus 0 - ${max} ${unitText}`;
          rec.insertAdjacentElement("afterend", fb);
        }
        this.attachRowHandlers(row);
      });
      this.updateOverallStatus(form);
      this.hookFormValidation(form);
      // Initialize totals for edit view
      try {
        this.updateTotals("pr");
      } catch (_) {}
    }

    // Compute and render totals for quick receive (qr) or create (pr)
    updateTotals(prefix) {
      const itemsContainer = document.getElementById(
        prefix === "qr" ? "qr-items-container" : "items-container",
      );
      if (!itemsContainer) return;
      let subtotal = 0;
      itemsContainer.querySelectorAll(".pr-row").forEach((row) => {
        const price = Math.round(
          parseFloat(row.getAttribute("data-price")) || 0,
        );
        const qty = Math.round(
          parseFloat(row.querySelector(".received-input")?.value) || 0,
        );
        // write back sanitized qty
        const qtyInput = row.querySelector(".received-input");
        if (qtyInput) qtyInput.value = this.formatQty(qty);
        subtotal += price * qty;
      });

      let addTotal = 0;
      const costContainer = document.getElementById(
        `${prefix}-additional-costs-container`,
      );
      if (costContainer) {
        costContainer
          .querySelectorAll('input[name^="additional_costs"][name$="[amount]"]')
          .forEach((inp) => {
            const v = Math.round(parseFloat(inp.value) || 0);
            inp.value = String(v);
            if (!isNaN(v)) addTotal += v;
          });
      }

      // Discount & Tax (optional)
      let discount = 0;
      let tax = 0;
      try {
        const discInp = document.querySelector('input[name="discount_amount"]');
        const taxInp = document.querySelector('input[name="tax_amount"]');
        const d = Math.round(parseFloat(discInp && discInp.value) || 0);
        const t = Math.round(parseFloat(taxInp && taxInp.value) || 0);
        if (discInp) discInp.value = String(d);
        if (taxInp) taxInp.value = String(t);
        if (!isNaN(d)) discount = d;
        if (!isNaN(t)) tax = t;
      } catch (_) {}

      const grand = subtotal + addTotal - discount + tax;
      const subEl = document.getElementById(`${prefix}-subtotal-amount`);
      const addEl = document.getElementById(`${prefix}-additional-amount`);
      const discEl = document.getElementById(`${prefix}-discount-amount`);
      const taxEl = document.getElementById(`${prefix}-tax-amount`);
      const grandEl = document.getElementById(`${prefix}-grand-total-amount`);
      if (subEl) subEl.textContent = this.formatCurrency(subtotal);
      if (addEl) addEl.textContent = this.formatCurrency(addTotal);
      if (discEl) discEl.textContent = this.formatCurrency(discount);
      if (taxEl) taxEl.textContent = this.formatCurrency(tax);
      if (grandEl) grandEl.textContent = this.formatCurrency(grand);
    }

    confirmDelete(id, receiptNumber) {
      if (!window.Swal) return;
      Swal.fire({
        title: "Konfirmasi Hapus",
        text: `Apakah Anda yakin ingin menghapus penerimaan "${receiptNumber}"?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          const form = document.getElementById("delete-form");
          if (!form) return;
          form.action = `/purchase-receipts/${id}`;
          Swal.fire({
            title: "Menghapus...",
            text: "Sedang memproses penghapusan",
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
              Swal.showLoading();
            },
          });
          form.submit();
        }
      });
    }
  }

  document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById("qr-additional-costs-container")) {
      setupAdditionalCosts("qr");
    }
    if (document.getElementById("pr-additional-costs-container")) {
      setupAdditionalCosts("pr");
    }

    // Initialize Purchase Receipt Manager and expose global helpers used by Blade
    const prManager = new PurchaseReceiptManager();
    // Expose globally for callbacks from dynamic UI
    window._prManager = prManager;
    window.openQuickReceive = (el) => prManager.openQuickReceive(el);
    window.confirmDelete = (id, num) => prManager.confirmDelete(id, num);
    prManager.getQuickReceiveModal();
    prManager.setupCreateView();
    prManager.setupEditView();
    // Hook validations
    prManager.hookFormValidation(document.getElementById("quickReceiveForm"));
    const createForm = document.querySelector(
      'form[action*="purchase-receipts"][enctype*="multipart/form-data"]',
    );
    prManager.hookFormValidation(createForm);

    // Initialize Bootstrap tooltips if available
    try {
      if (window.bootstrap && typeof bootstrap.Tooltip === "function") {
        const tooltipTriggerList = Array.prototype.slice.call(
          document.querySelectorAll('[data-bs-toggle="tooltip"]'),
        );
        tooltipTriggerList.forEach(function (triggerEl) {
          new bootstrap.Tooltip(triggerEl);
        });
      }
    } catch (e) {
      console.warn("Tooltip initialization failed:", e);
    }

    // Recalc totals when discount/tax changed (works for both modal and create)
    try {
      ["discount_amount", "tax_amount"].forEach((name) => {
        document.querySelectorAll(`input[name="${name}"]`).forEach((inp) => {
          ["input", "change", "blur"].forEach((ev) =>
            inp.addEventListener(ev, () => {
              try {
                window._prManager && window._prManager.updateTotals("qr");
              } catch (_) {}
              try {
                window._prManager && window._prManager.updateTotals("pr");
              } catch (_) {}
            }),
          );
        });
      });
    } catch (_) {}
  });
})();
