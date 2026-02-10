(function () {
  class PurchaseOrderManager {
    constructor() {
      this.currentSupplierId = null;
      this.allMaterials = window.rawMaterials || [];
      this.filteredMaterials = [];
      this.itemCounter = 0;

      this.elements = {
        supplierSelect: document.getElementById("supplier_id"),
        supplierPhoneInput: document.getElementById("supplier_phone"),
        addItemBtn: document.getElementById("add-item"),
        validatePricesBtn: document.getElementById("validate-prices"),
        itemsTableBody: document.getElementById("item-rows"),
        itemTemplate: document.getElementById("item-template"),
        totalDisplay: document.getElementById("grand-total"),
        form: document.getElementById("purchase-order-form"),
        draftButton: document.querySelector('button[value="save_draft"]'),
        orderButton: document.querySelector('button[value="order_now"]'),
      };

      this.init();
    }

    init() {
      // load already provided rawMaterials
      this.allMaterials = window.rawMaterials || [];

      // If supplier pre-selected (edit flow), apply it
      if (this.elements.supplierSelect && this.elements.supplierSelect.value) {
        this.currentSupplierId = this.elements.supplierSelect.value;
        this.filterMaterialsBySupplier(this.currentSupplierId);
        // attempt background refresh but do not block UI
        this.reloadMaterialsData(this.currentSupplierId)
          .then(() => {
            this.filterMaterialsBySupplier(this.currentSupplierId);
            this.refreshRowSelectOptions();
          })
          .catch(() => {});
      }

      // calculate starting itemCounter from existing rows (edit)
      if (this.elements.itemsTableBody) {
        const rows = this.elements.itemsTableBody.querySelectorAll(".item-row");
        let maxIndex = -1;
        rows.forEach((row) => {
          const anyField = row.querySelector('[name^="items["]');
          if (anyField && anyField.name) {
            const m = anyField.name.match(/items\[(\d+)\]/);
            if (m) maxIndex = Math.max(maxIndex, parseInt(m[1], 10));
          }
        });
        this.itemCounter = maxIndex + 1;
      }

      // initialize existing rows if any
      this.initializeExistingRows();

      this.bindEvents();
      this.updateUIState();
    }

    initializeExistingRows() {
      if (!this.elements.itemsTableBody) return;
      const rows = this.elements.itemsTableBody.querySelectorAll(".item-row");
      rows.forEach((row) => {
        const sel = row.querySelector(".raw-material-select");
        if (sel) {
          this.populateMaterialSelect(sel);
          // ensure unit cell matches selection
          if (sel.value) {
            const opt = sel.selectedOptions[0];
            const unitCell = row.querySelector(".unit-name");
            const unitName = opt?.dataset?.unit || "-";
            if (unitCell) unitCell.textContent = unitName;
          }
        }
        this.bindItemRowEvents(row);
      });
      this.updateGrandTotal();
    }

    bindEvents() {
      if (this.elements.supplierSelect) {
        this.elements.supplierSelect.addEventListener("change", (e) =>
          this.handleSupplierChange(e.target.value),
        );
      }
      if (this.elements.addItemBtn) {
        this.elements.addItemBtn.addEventListener("click", (e) => {
          e.preventDefault();
          this.addNewItem();
        });
      }
      if (this.elements.validatePricesBtn) {
        this.elements.validatePricesBtn.addEventListener("click", (e) => {
          e.preventDefault();
          this.validatePrices();
        });
      }
      if (this.elements.form) {
        this.elements.form.addEventListener("submit", (e) => {
          // e.submitter supported in modern browsers
          const submitAction = e.submitter ? e.submitter.value : null;
          if (!submitAction) {
            // fallback: determine by focused button
            const active = document.activeElement;
            const draftBtn = this.elements.draftButton;
            const orderBtn = this.elements.orderButton;
            if (active === draftBtn) return this.handleDraftSubmit(e);
            if (active === orderBtn) return this.handleOrderSubmit(e);
            // default to draft if unknown
            return this.handleDraftSubmit(e);
          }
          if (submitAction === "save_draft") return this.handleDraftSubmit(e);
          if (submitAction === "order_now") return this.handleOrderSubmit(e);
        });
      }
    }

    handleDraftSubmit(e) {
      // allow normal submit if valid
      if (!this.validateForm()) {
        e.preventDefault();
      }
    }

    handleOrderSubmit(e) {
      e.preventDefault();
      this.submitOrder();
    }

    handleSupplierChange(newSupplierId) {
      const hasNonEmptyRows = Array.from(
        this.elements.itemsTableBody?.children || [],
      ).some((r) => !r.classList.contains("empty-row"));
      if (
        this.currentSupplierId &&
        this.currentSupplierId !== newSupplierId &&
        hasNonEmptyRows
      ) {
        // confirm reset
        if (window.Swal) {
          Swal.fire({
            title: "⚠️ Konfirmasi Perubahan Supplier",
            text: "Mengganti supplier akan menghapus semua item yang sudah ditambahkan. Lanjutkan?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Ganti Supplier",
            cancelButtonText: "Batal",
          }).then((res) => {
            if (res.isConfirmed) this.applySupplierChange(newSupplierId);
            else
              this.elements.supplierSelect.value = this.currentSupplierId || "";
          });
        } else {
          if (
            !confirm(
              "Mengganti supplier akan menghapus semua item yang sudah ditambahkan. Lanjutkan?",
            )
          ) {
            this.elements.supplierSelect.value = this.currentSupplierId || "";
            return;
          }
          this.applySupplierChange(newSupplierId);
        }
        return;
      }
      this.applySupplierChange(newSupplierId);
    }

    applySupplierChange(supplierId) {
      this.currentSupplierId = supplierId || null;
      if (!supplierId) {
        // clear phone and items
        if (this.elements.supplierPhoneInput)
          this.elements.supplierPhoneInput.value = "";
        this.resetAllItems();
        this.updateUIState();
        return;
      }
      // update phone from selected option
      const opt = this.elements.supplierSelect?.selectedOptions[0];
      if (opt && this.elements.supplierPhoneInput) {
        this.elements.supplierPhoneInput.value = opt.dataset.phone || "";
      }
      // fetch latest materials then reset items
      this.showLoadingState("Mengambil data bahan mentah terbaru...");
      this.reloadMaterialsData(supplierId)
        .then(() => {
          this.filterMaterialsBySupplier(supplierId);
          this.resetAllItems();
          this.updateUIState();
          if (window.Swal) Swal.close();
        })
        .catch(() => {
          // fallback local filter
          this.filterMaterialsBySupplier(supplierId);
          this.resetAllItems();
          this.updateUIState();
          if (window.Swal) Swal.close();
        });
    }

    filterMaterialsBySupplier(supplierId) {
      this.filteredMaterials = (this.allMaterials || []).filter(
        (m) => String(m.supplier_id) === String(supplierId),
      );
      return this.filteredMaterials;
    }

    reloadMaterialsData(supplierId) {
      console.log(supplierId);
      return new Promise((resolve, reject) => {
        const token =
          document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "";
        fetch(`/materials-by-supplier/${supplierId}`, {
          method: "GET",
          headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token,
          },
        })
          .then((r) => {
            if (!r.ok) throw new Error("Network response not ok");
            return r.json();
          })
          .then((data) => {
            let arr = [];
            if (Array.isArray(data)) arr = data;
            else if (data && Array.isArray(data.data)) arr = data.data;
            else if (data && Array.isArray(data.materials))
              arr = data.materials;
            else if (data && typeof data === "object") {
              const vals = Object.values(data).filter(
                (v) => Array.isArray(v) === false,
              );
              if (Array.isArray(vals) && vals.length && vals[0] && vals[0].id)
                arr = Object.values(data);
            }

            // merge arr into allMaterials (update existing / add new)
            if (arr && arr.length) {
              arr.forEach((mat) => {
                const idx = this.allMaterials.findIndex(
                  (m) => m && m.id === mat.id,
                );
                if (idx !== -1) this.allMaterials[idx] = mat;
                else this.allMaterials.push(mat);
              });
              // set filteredMaterials to server result
              this.filteredMaterials = arr;
              resolve(arr);
            } else {
              // even empty array is a valid response
              this.filteredMaterials = arr;
              resolve(arr);
            }
          })
          .catch((err) => reject(err));
      });
    }

    resetAllItems() {
      if (!this.elements.itemsTableBody) return;
      this.elements.itemsTableBody.innerHTML = `
                <tr class="empty-row">
                    <td colspan="7" class="p-8 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="bi bi-cart-x text-3xl mb-3 opacity-50"></i>
                            <h6 class="font-medium">Belum ada item pesanan</h6>
                            <p class="text-sm">Klik tombol "Tambah Bahan Mentah" untuk menambah item</p>
                        </div>
                    </td>
                </tr>
            `;
      this.itemCounter = 0;
      this.updateGrandTotal();
    }

    addNewItem() {
      if (!this.currentSupplierId) {
        if (window.Swal) {
          Swal.fire({
            title: "Supplier Diperlukan",
            text: "Silakan pilih supplier terlebih dahulu.",
            icon: "warning",
          });
        } else alert("Silakan pilih supplier terlebih dahulu.");
        return;
      }
      // ensure we have materials
      if (!this.filteredMaterials || this.filteredMaterials.length === 0) {
        this.filterMaterialsBySupplier(this.currentSupplierId);
        if (!this.filteredMaterials || this.filteredMaterials.length === 0) {
          if (window.Swal)
            Swal.fire({
              title: "Tidak Ada Bahan Mentah",
              text: "Tidak ada bahan mentah untuk supplier ini.",
              icon: "info",
            });
          else alert("Tidak ada bahan mentah untuk supplier ini.");
          return;
        }
      }

      // remove empty row if present
      const empty = this.elements.itemsTableBody.querySelector(".empty-row");
      if (empty) empty.remove();

      const html = this.elements.itemTemplate.innerHTML.replace(
        /__index__/g,
        this.itemCounter,
      );
      this.elements.itemsTableBody.insertAdjacentHTML("beforeend", html);
      const newRow = this.elements.itemsTableBody.lastElementChild;
      // populate select
      const sel = newRow.querySelector(".raw-material-select");
      if (sel) this.populateMaterialSelect(sel);
      this.updateRowNumbers();
      this.bindItemRowEvents(newRow);
      this.itemCounter++;
      this.updateGrandTotal();
    }

    populateMaterialSelect(selectElement) {
      if (!selectElement) return;
      selectElement.innerHTML =
        '<option value="">-- Pilih Bahan Mentah --</option>';
      (this.filteredMaterials || []).forEach((material) => {
        if (!material) return;
        const option = document.createElement("option");
        option.value = material.id;
        option.textContent =
          (material.name || "Unknown") +
          (material.code ? ` (${material.code})` : "");
        // unit dataset
        let unitName = "-";
        if (material.unit)
          unitName = material.unit.unit_name || material.unit.name || "-";
        option.dataset.unit = unitName;
        // price dataset - use unit_price or material.unit_price
        const price = Number(material.unit_price ?? material.price ?? 0);
        option.dataset.price = String(Math.round(price));
        option.dataset.stock = material.current_stock ?? 0;
        selectElement.appendChild(option);
      });
    }

    refreshRowSelectOptions() {
      if (!this.elements.itemsTableBody) return;
      const rows = this.elements.itemsTableBody.querySelectorAll(".item-row");
      rows.forEach((row) => {
        const sel = row.querySelector(".raw-material-select");
        if (!sel) return;
        const cur = sel.value;
        this.populateMaterialSelect(sel);
        if (cur) {
          sel.value = cur;
          // update unit cell
          const unitCell = row.querySelector(".unit-name");
          const opt = sel.selectedOptions[0];
          if (unitCell) unitCell.textContent = opt?.dataset?.unit || "-";
        }
      });
    }

    bindItemRowEvents(row) {
      const sel = row.querySelector(".raw-material-select");
      if (sel) {
        sel.addEventListener("change", (e) =>
          this.handleMaterialChange(e, row),
        );
        // lazy-populate on focus if options missing
        sel.addEventListener("focus", () => {
          if (sel.options.length <= 1) {
            this.populateMaterialSelect(sel);
          }
        });
      }
      const qty = row.querySelector(".item-quantity");
      const price = row.querySelector(".item-price");
      [qty, price].forEach((el) => {
        if (!el) return;
        el.setAttribute("inputmode", "numeric");
        el.addEventListener("input", () => this.updateRowTotal(row));
      });
      // remove button is .btn-remove in template
      const removeBtn = row.querySelector(".btn-remove");
      if (removeBtn) {
        removeBtn.addEventListener("click", (e) => {
          e.preventDefault();
          this.removeItem(row);
        });
      }
    }

    handleMaterialChange(event, row) {
      const opt = event.target.selectedOptions[0];
      if (!opt) return;
      // unit
      const unitCell = row.querySelector(".unit-name");
      const unit = opt.dataset.unit || "-";
      if (unitCell) unitCell.textContent = unit;
      // price - only override when user action
      const priceInput = row.querySelector(".item-price");
      if (priceInput && event.isTrusted) {
        const p = Number(opt.dataset.price || 0);
        priceInput.value = String(Math.round(p));
      }
      this.updateRowTotal(row);
    }

    removeItem(row) {
      const doRemove = () => {
        row.remove();
        this.updateRowNumbers();
        this.updateGrandTotal();
        if ((this.elements.itemsTableBody.children || []).length === 0)
          this.resetAllItems();
      };
      if (window.Swal) {
        Swal.fire({
          title: "Hapus Item",
          text: "Yakin ingin menghapus item ini?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Hapus",
          cancelButtonText: "Batal",
        }).then((res) => {
          if (res.isConfirmed) doRemove();
        });
      } else {
        if (confirm("Yakin ingin menghapus item ini?")) doRemove();
      }
    }

    updateRowNumbers() {
      const rows = this.elements.itemsTableBody.querySelectorAll(".item-row");
      rows.forEach((r, i) => {
        const n = r.querySelector(".row-number");
        if (n) n.textContent = String(i + 1);
      });
    }

    updateRowTotal(row) {
      const qty = Math.max(
        0,
        Math.round(Number(row.querySelector(".item-quantity")?.value || 0)),
      );
      const price = Math.max(
        0,
        Math.round(Number(row.querySelector(".item-price")?.value || 0)),
      );
      if (row.querySelector(".item-quantity"))
        row.querySelector(".item-quantity").value = String(qty);
      if (row.querySelector(".item-price"))
        row.querySelector(".item-price").value = String(price);
      const total = qty * price;
      const cell = row.querySelector(".item-total");
      if (cell) cell.textContent = this.formatCurrency(total);
      this.updateGrandTotal();
    }

    updateGrandTotal() {
      const rows = this.elements.itemsTableBody.querySelectorAll(".item-row");
      let grand = 0;
      rows.forEach((r) => {
        const q = Math.round(
          Number(r.querySelector(".item-quantity")?.value || 0),
        );
        const p = Math.round(
          Number(r.querySelector(".item-price")?.value || 0),
        );
        grand += q * p;
      });
      if (this.elements.totalDisplay)
        this.elements.totalDisplay.textContent = this.formatCurrency(grand);
    }

    validatePrices() {
      if (!this.currentSupplierId) {
        if (window.Swal)
          Swal.fire({
            title: "Supplier Diperlukan",
            text: "Pilih supplier terlebih dahulu",
            icon: "warning",
          });
        else alert("Pilih supplier terlebih dahulu");
        return;
      }
      const rows = this.elements.itemsTableBody.querySelectorAll(".item-row");
      if (!rows.length) {
        if (window.Swal)
          Swal.fire({
            title: "Tidak Ada Item",
            text: "Tambahkan item terlebih dahulu",
            icon: "info",
          });
        else alert("Tambahkan item terlebih dahulu");
        return;
      }
      if (window.Swal) {
        Swal.fire({
          title: "Memvalidasi Harga",
          text: "Memperbarui data harga...",
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading(),
        });
      }
      this.reloadMaterialsData(this.currentSupplierId)
        .then(() => {
          // update each row price if material matches
          let updated = 0;
          rows.forEach((row) => {
            const sel = row.querySelector(".raw-material-select");
            const priceInput = row.querySelector(".item-price");
            if (!sel || !sel.value || !priceInput) return;
            const mat = this.filteredMaterials.find(
              (m) => String(m.id) === String(sel.value),
            );
            const newPrice = Math.round(
              Number(mat?.unit_price ?? mat?.price ?? 0),
            );
            const prev = Math.round(Number(priceInput.value || 0));
            priceInput.value = String(newPrice);
            if (prev !== newPrice) updated++;
            this.updateRowTotal(row);
          });
          if (window.Swal) Swal.close();
          if (window.Swal)
            Swal.fire({
              title: "Harga Diperbarui",
              text:
                updated > 0
                  ? `Memperbarui ${updated} harga`
                  : "Semua harga sudah terkini",
              icon: "success",
            });
        })
        .catch(() => {
          if (window.Swal) Swal.close();
          if (window.Swal)
            Swal.fire({
              title: "Gagal Memperbarui Harga",
              text: "Terjadi kesalahan saat memuat data",
              icon: "error",
            });
        });
    }

    validateForm() {
      if (!this.currentSupplierId) {
        this.showErrorMessage("Pilih supplier terlebih dahulu");
        return false;
      }
      const rows = this.elements.itemsTableBody.querySelectorAll(".item-row");
      if (!rows.length) {
        this.showErrorMessage("Tambahkan minimal satu item pesanan");
        return false;
      }
      for (const row of rows) {
        const mat = row.querySelector(".raw-material-select")?.value;
        const q = Math.round(
          Number(row.querySelector(".item-quantity")?.value || 0),
        );
        const p = Math.round(
          Number(row.querySelector(".item-price")?.value || 0),
        );
        if (!mat || q <= 0 || p < 0) {
          this.showErrorMessage(
            "Pastikan semua item memiliki bahan mentah, kuantitas > 0, dan harga valid",
          );
          return false;
        }
      }
      return true;
    }

    saveDraft() {
      // ensure hidden input submit_action exists
      let input = this.elements.form.querySelector(
        'input[name="submit_action"]',
      );
      if (!input) {
        input = document.createElement("input");
        input.type = "hidden";
        input.name = "submit_action";
        this.elements.form.appendChild(input);
      }
      input.value = "save_draft";
      this.elements.form.submit();
    }

    submitOrder() {
      const supplierName =
        this.elements.supplierSelect?.selectedOptions?.[0]?.textContent?.trim() ||
        "Supplier";
      if (window.Swal) {
        Swal.fire({
          title: "Konfirmasi Pesanan",
          html: `<p>Pesanan akan dikirim ke <strong>${supplierName}</strong> via WhatsApp.</p>`,
          icon: "question",
          showCancelButton: true,
          confirmButtonText: "Kirim Pesanan",
          cancelButtonText: "Batal",
        }).then((res) => {
          if (res.isConfirmed) {
            let input = this.elements.form.querySelector(
              'input[name="submit_action"]',
            );
            if (!input) {
              input = document.createElement("input");
              input.type = "hidden";
              input.name = "submit_action";
              this.elements.form.appendChild(input);
            }
            input.value = "order_now";
            this.elements.form.submit();
          }
        });
      } else {
        if (confirm(`Kirim pesanan ke ${supplierName}?`)) {
          let input = this.elements.form.querySelector(
            'input[name="submit_action"]',
          );
          if (!input) {
            input = document.createElement("input");
            input.type = "hidden";
            input.name = "submit_action";
            this.elements.form.appendChild(input);
          }
          input.value = "order_now";
          this.elements.form.submit();
        }
      }
    }

    showErrorMessage(message) {
      if (window.Swal)
        Swal.fire({ icon: "error", title: "Error", text: message });
      else alert(message);
    }

    formatCurrency(amount) {
      return (
        "Rp " + new Intl.NumberFormat("id-ID").format(Math.round(amount || 0))
      );
    }

    showLoadingState(msg) {
      if (window.Swal)
        Swal.fire({
          title: "Memproses...",
          text: msg,
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading(),
        });
    }

    updateUIState() {
      const hasSupplier = !!this.currentSupplierId;
      if (this.elements.validatePricesBtn)
        this.elements.validatePricesBtn.disabled = !hasSupplier;
      if (this.elements.addItemBtn)
        this.elements.addItemBtn.disabled = !hasSupplier;
      if (this.elements.orderButton)
        this.elements.orderButton.disabled = !hasSupplier;
    }
  }

  // init on DOM ready, but don't override if already present
  document.addEventListener("DOMContentLoaded", function () {
    if (!document.getElementById("purchase-order-form")) return;
    if (!window.purchaseOrderManager) {
      window.purchaseOrderManager = new PurchaseOrderManager();
    }
  });
})();
