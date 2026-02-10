(function () {
  // Shared Production Requests Form logic for create & edit pages
  const fmt = (n) => {
    const v = Number(n) || 0;
    return (
      "Rp " +
      new Intl.NumberFormat("id-ID", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      }).format(Math.round(v))
    );
  };

  function getMaxIndex(selector) {
    let maxIdx = -1;
    document.querySelectorAll(selector).forEach((row) => {
      const idxStr = row.getAttribute("data-index");
      const idx = idxStr ? parseInt(idxStr, 10) : -1;
      if (!isNaN(idx) && idx > maxIdx) maxIdx = idx;
    });
    return maxIdx;
  }

  // Material rows
  let materialRowIndex = 0;
  function updateRemoveButtonStates() {
    const rows = document.querySelectorAll(".material-row");
    rows.forEach((row) => {
      const removeBtn = row.querySelector(
        'button[onclick*="removeMaterialRow"]',
      );
      if (removeBtn) removeBtn.disabled = rows.length <= 1;
    });
  }

  function addMaterialRow() {
    const container = document.getElementById("materials-container");
    if (!container) return;
    const template = container.querySelector(".material-row");
    if (!template) return;

    const newRow = template.cloneNode(true);
    materialRowIndex++;
    newRow.setAttribute("data-index", materialRowIndex);

    newRow.querySelectorAll("select, input").forEach((el) => {
      if (el.name)
        el.name = el.name.replace(/\[\d+\]/, `[${materialRowIndex}]`);
      if (el.id) el.id = el.id.replace(/\d+/, materialRowIndex);
      if (el.type !== "button") el.value = "";
      el.classList.remove("is-invalid");
      if (el.classList.contains("quantity-input")) el.removeAttribute("max");
    });

    // Fix IDs: unit info, warning, total
    const unitInfo = newRow.querySelector('[id*="unit-info"]');
    if (unitInfo) {
      unitInfo.id = `unit-info-${materialRowIndex}`;
      unitInfo.textContent = "";
    }
    const warning = newRow.querySelector('[id*="stock-warning"]');
    if (warning) {
      warning.id = `stock-warning-${materialRowIndex}`;
      warning.textContent = "";
      warning.classList.add("d-none");
    }
    const total =
      newRow.querySelector('[id*="total-"]') ||
      newRow.querySelector('[id*="total"]');
    if (total) {
      total.id = `total-${materialRowIndex}`;
      total.value = "";
    }

    // Rewire per-row handlers (keep onclick API)
    const materialSelect = newRow.querySelector(".material-select");
    if (materialSelect)
      materialSelect.setAttribute(
        "onchange",
        `updateMaterialInfo(${materialRowIndex})`,
      );
    const qtyInput = newRow.querySelector(".quantity-input");
    if (qtyInput)
      qtyInput.setAttribute(
        "onchange",
        `calculateRowTotal(${materialRowIndex})`,
      );
    const costInput = newRow.querySelector(".cost-input");
    if (costInput)
      costInput.setAttribute(
        "onchange",
        `calculateRowTotal(${materialRowIndex})`,
      );

    // Enforce integer-only attributes on new inputs and sanitize values
    if (qtyInput) {
      qtyInput.setAttribute("step", "1");
      qtyInput.setAttribute("min", "0");
      qtyInput.setAttribute("inputmode", "numeric");
      qtyInput.setAttribute("pattern", "[0-9]*");
      qtyInput.value = String(Math.round(parseFloat(qtyInput.value) || 0));
    }
    if (costInput) {
      costInput.setAttribute("step", "1");
      costInput.setAttribute("min", "0");
      costInput.setAttribute("inputmode", "numeric");
      costInput.setAttribute("pattern", "[0-9]*");
      costInput.value = String(Math.round(parseFloat(costInput.value) || 0));
    }

    const removeBtn = newRow.querySelector(
      'button[onclick*="removeMaterialRow"]',
    );
    if (removeBtn) {
      removeBtn.setAttribute(
        "onclick",
        `removeMaterialRow(${materialRowIndex})`,
      );
      removeBtn.disabled = false;
    }

    container.appendChild(newRow);
    updateRemoveButtonStates();
  }

  function removeMaterialRow(index) {
    const row =
      document.querySelector(`.material-row[data-index="${index}"]`) ||
      document.querySelector(`[data-index="${index}"]`);
    if (row) row.remove();
    calculateGrandTotal();
    updateRemoveButtonStates();
  }

  function updateMaterialInfo(index) {
    const select = document.querySelector(
      `select[name="items[${index}][raw_material_id]"]`,
    );
    const selectedOption =
      select && select.options ? select.options[select.selectedIndex] : null;
    const unitInfo = document.getElementById(`unit-info-${index}`);
    const costInput = document.querySelector(
      `input[name="items[${index}][unit_cost]"]`,
    );
    const quantityInput = document.querySelector(
      `input[name="items[${index}][requested_quantity]"]`,
    );
    const warningEl = document.getElementById(`stock-warning-${index}`);

    if (selectedOption && selectedOption.value) {
      const unit = selectedOption.dataset.unit || "";
      const cost = parseFloat(selectedOption.dataset.cost || "0");
      const stockStr = selectedOption.dataset.stock;
      const s = parseFloat(stockStr || "0");

      if (unitInfo)
        unitInfo.textContent = `Unit: ${unit} (Stok: ${Math.round(isNaN(s) ? 0 : s).toLocaleString("id-ID")})`;
      if (costInput) {
        // Enforce integer-only attributes and sanitize value
        costInput.setAttribute("step", "1");
        costInput.setAttribute("min", "0");
        costInput.setAttribute("inputmode", "numeric");
        costInput.setAttribute("pattern", "[0-9]*");
        costInput.value = isNaN(cost) ? "" : String(Math.round(cost));
      }

      if (quantityInput) {
        // Enforce integer-only attributes for quantity
        quantityInput.setAttribute("step", "1");
        quantityInput.setAttribute("min", "0");
        quantityInput.setAttribute("inputmode", "numeric");
        quantityInput.setAttribute("pattern", "[0-9]*");
        if (!isNaN(s)) {
          const sInt = Math.round(s);
          quantityInput.max = String(sInt);
          const current =
            Math.round(parseFloat(quantityInput.value || "")) || 0;
          if (!isNaN(current) && current > s) {
            quantityInput.value = String(sInt);
            if (warningEl) {
              warningEl.textContent = `Jumlah melebihi stok. Maksimum ${sInt.toLocaleString("id-ID")}`;
              warningEl.classList.remove("d-none");
            }
          } else if (s <= 0) {
            quantityInput.value = "";
            if (warningEl) {
              warningEl.textContent = "Stok tidak memadai";
              warningEl.classList.remove("d-none");
            }
          } else {
            if (warningEl) {
              warningEl.textContent = "";
              warningEl.classList.add("d-none");
            }
          }
        } else {
          quantityInput.removeAttribute("max");
          if (warningEl) {
            warningEl.textContent = "";
            warningEl.classList.add("d-none");
          }
        }
      }
      calculateRowTotal(index);
    } else {
      if (unitInfo) unitInfo.textContent = "";
      if (costInput) costInput.value = "";
      if (quantityInput) quantityInput.removeAttribute("max");
      if (warningEl) {
        warningEl.textContent = "";
        warningEl.classList.add("d-none");
      }
    }
  }

  function calculateRowTotal(index) {
    const quantityInput = document.querySelector(
      `input[name="items[${index}][requested_quantity]"]`,
    );
    const costInput = document.querySelector(
      `input[name="items[${index}][unit_cost]"]`,
    );
    const totalDisplay = document.getElementById(`total-${index}`);
    const warningEl = document.getElementById(`stock-warning-${index}`);

    if (!quantityInput || !costInput || !totalDisplay) {
      calculateGrandTotal();
      return;
    }

    // Sanitize to integers
    let quantity = Math.round(parseFloat(quantityInput.value) || 0);
    const maxAttrRaw = quantityInput.getAttribute("max") || "";
    const maxAttr =
      maxAttrRaw === "" ? NaN : Math.round(parseFloat(maxAttrRaw));
    if (!isNaN(maxAttr) && quantity > maxAttr) {
      quantity = maxAttr;
      quantityInput.value = String(maxAttr);
      if (warningEl) {
        warningEl.textContent = `Jumlah melebihi stok. Maksimum ${maxAttr.toLocaleString("id-ID")}`;
        warningEl.classList.remove("d-none");
      }
    } else if (!isNaN(maxAttr) && maxAttr <= 0) {
      if (warningEl) {
        warningEl.textContent = "Stok tidak memadai";
        warningEl.classList.remove("d-none");
      }
    } else {
      if (warningEl) {
        warningEl.textContent = "";
        warningEl.classList.add("d-none");
      }
    }

    const cost = Math.round(parseFloat(costInput.value) || 0);
    // write back sanitized values
    quantityInput.value = String(quantity);
    costInput.value = String(cost);
    const total = quantity * cost;
    totalDisplay.value = fmt(total);

    calculateGrandTotal();
  }

  function calculateGrandTotal() {
    let grand = 0;
    document.querySelectorAll(".material-row").forEach((row) => {
      const q = Math.round(
        parseFloat(
          row.querySelector('input[name*="requested_quantity"]')?.value || 0,
        ),
      );
      const c = Math.round(
        parseFloat(row.querySelector('input[name*="unit_cost"]')?.value || 0),
      );
      grand += q * c;
    });
    const gt = document.getElementById("grand-total");
    if (gt) gt.textContent = fmt(grand);
  }

  // Outputs rows
  let outputRowIndex = 0;
  function updateOutputRemoveButtonStates() {
    const rows = document.querySelectorAll(".output-row");
    rows.forEach((r) => {
      const btn = r.querySelector('button[onclick*="removeOutputRow"]');
      if (btn) btn.disabled = rows.length <= 1;
    });
  }

  function addOutputRow() {
    const container = document.getElementById("outputs-container");
    if (!container) return;
    const template = container.querySelector(".output-row");
    if (!template) return;

    const newRow = template.cloneNode(true);
    outputRowIndex++;
    newRow.setAttribute("data-index", outputRowIndex);
    newRow.querySelectorAll("select, input").forEach((el) => {
      if (el.name) el.name = el.name.replace(/\[\d+\]/g, `[${outputRowIndex}]`);
      if (el.type !== "button") el.value = "";
      el.classList.remove("is-invalid");
    });

    // Fix unit info element id and clear text
    const unitInfo = newRow.querySelector(".output-unit-info");
    if (unitInfo) {
      unitInfo.id = `output-unit-${outputRowIndex}`;
      unitInfo.textContent = "";
    }

    // Rewire product select change handler with the new index
    const productSelect = newRow.querySelector(".output-product-select");
    if (productSelect) {
      productSelect.setAttribute(
        "onchange",
        `updateOutputUnit(${outputRowIndex})`,
      );
    }

    const removeBtn = newRow.querySelector(
      'button[onclick*="removeOutputRow"]',
    );
    if (removeBtn) {
      removeBtn.setAttribute("onclick", `removeOutputRow(${outputRowIndex})`);
      removeBtn.disabled = false;
    }

    // Enforce integer-only attributes for planned quantity in outputs and sanitize
    const plannedQty = newRow.querySelector(
      'input[name*="[planned_quantity]"]',
    );
    if (plannedQty) {
      plannedQty.setAttribute("step", "1");
      plannedQty.setAttribute("min", "0");
      plannedQty.setAttribute("inputmode", "numeric");
      plannedQty.setAttribute("pattern", "[0-9]*");
      plannedQty.value = String(Math.round(parseFloat(plannedQty.value) || 0));
    }

    container.appendChild(newRow);
    updateOutputRemoveButtonStates();
  }

  function removeOutputRow(index) {
    const row = document.querySelector(`.output-row[data-index="${index}"]`);
    if (row) row.remove();
    updateOutputRemoveButtonStates();
  }

  // Update unit display for output row
  function updateOutputUnit(index) {
    const select = document.querySelector(
      `select[name="outputs[${index}][semi_finished_product_id]"]`,
    );
    if (!select) return;
    
    const opt =
      select && select.options ? select.options[select.selectedIndex] : null;
    const infoEl = document.getElementById(`output-unit-${index}`);
    if (!infoEl) return;
    if (opt && opt.value) {
      const unitName = opt.getAttribute("data-unit") || "";
      const unitAbbr = opt.getAttribute("data-unit-abbr") || "";
      const text = unitAbbr
        ? `Satuan: ${unitName} (${unitAbbr})`
        : unitName
          ? `Satuan: ${unitName}`
          : "";
      infoEl.textContent = text;
    } else {
      infoEl.textContent = "";
    }
  }

  // Form validation + prune empty outputs
  function attachFormValidation() {
    const form = document.getElementById("productionRequestForm");
    if (!form) return;

    form.addEventListener("submit", function (e) {
      const rows = document.querySelectorAll(".material-row");
      if (rows.length === 0) {
        e.preventDefault();
        alert("Harap tambahkan minimal satu bahan mentah!");
        return;
      }

      let hasValidRow = false;
      rows.forEach((row) => {
        const materialSelect = row.querySelector(
          'select[name*="raw_material_id"]',
        );
        const quantityInput = row.querySelector(
          'input[name*="requested_quantity"]',
        );
        const costInput = row.querySelector('input[name*="unit_cost"]');
        if (materialSelect?.value && quantityInput?.value && costInput?.value) {
          hasValidRow = true;
        }
      });

      if (!hasValidRow) {
        e.preventDefault();
        alert("Harap lengkapi minimal satu baris bahan mentah dengan benar!");
        return;
      }

      // Prune empty outputs
      const outputRows = document.querySelectorAll(".output-row");
      let hasNonEmptyOutput = false;
      outputRows.forEach((row) => {
        const productSelect = row.querySelector(
          'select[name*="[semi_finished_product_id]"]',
        );
        const qtyInput = row.querySelector('input[name*="[planned_quantity]"]');
        const productVal = productSelect ? productSelect.value : "";
        const qtyVal = qtyInput ? qtyInput.value : "";
        if (!productVal && !qtyVal) {
          row.remove();
        } else {
          hasNonEmptyOutput = true;
        }
      });
      if (!hasNonEmptyOutput) {
        const outputsContainer = document.getElementById("outputs-container");
        if (outputsContainer) {
          outputsContainer
            .querySelectorAll('select[name^="outputs"], input[name^="outputs"]')
            .forEach((el) => {
              el.name = "";
            });
        }
      }
    });
  }

  function init() {
    // Initialize indexes from existing DOM
    materialRowIndex = Math.max(getMaxIndex(".material-row"), 0);
    outputRowIndex = Math.max(getMaxIndex(".output-row"), 0);

    updateRemoveButtonStates();
    updateOutputRemoveButtonStates();

    // Initialize constraints/info for existing rows
    document.querySelectorAll(".material-row").forEach((row) => {
      const idx = row.getAttribute("data-index");
      if (idx !== null) {
        try {
          updateMaterialInfo(idx);
        } catch (e) {}
      }
    });

    // Initialize unit display for existing output rows
    document.querySelectorAll(".output-row").forEach((row) => {
      const idx = row.getAttribute("data-index");
      if (idx !== null) {
        try {
          updateOutputUnit(idx);
        } catch (e) {}
        // Ensure planned quantity inputs are integer-only
        const plannedQty = row.querySelector(
          'input[name*="[planned_quantity]"]',
        );
        if (plannedQty) {
          plannedQty.setAttribute("step", "1");
          plannedQty.setAttribute("min", "0");
          plannedQty.setAttribute("inputmode", "numeric");
          plannedQty.setAttribute("pattern", "[0-9]*");
          plannedQty.value = String(
            Math.round(parseFloat(plannedQty.value) || 0),
          );
        }
      }
    });

    attachFormValidation();
  }

  // Expose functions globally for existing onclick attributes
  window.addMaterialRow = addMaterialRow;
  window.removeMaterialRow = removeMaterialRow;
  window.updateMaterialInfo = updateMaterialInfo;
  window.calculateRowTotal = calculateRowTotal;
  window.calculateGrandTotal = calculateGrandTotal;
  window.addOutputRow = addOutputRow;
  window.removeOutputRow = removeOutputRow;
  window.updateOutputUnit = updateOutputUnit;

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
