/**
 * Semi-finished Usage Requests Form - Client-side functionality
 * Handles dynamic item management and form validation for semi-finished usage requests
 */

// Guard: ensure jQuery is available
if (!window.jQuery) {
    try { console.error('[semi-finished-usage-requests-form] jQuery not found. Ensure it is loaded before this script.'); } catch (_) {}
}

$(document).ready(function() {
    // Keep track of the row index for adding new items (use preset from edit page when available)
    let rowIndex = (typeof window.rowIndex === 'number') ? window.rowIndex : ($('.item-row').length || 0);
    try { console.debug('[semi-finished-usage-requests-form] initial rowIndex =', rowIndex); } catch (_) {}
    // Keep track of output rows index (optional section). Prefer outputs; fallback to targets (legacy).
    let outputIndex = (typeof window.outputIndex === 'number')
        ? window.outputIndex
        : ($('.output-row').length || $('#outputsTableBody .output-row').length || $('.target-row').length || 0);
    try { console.debug('[semi-finished-usage-requests-form] initial outputIndex =', outputIndex); } catch (_) {}
    // Backward-compat: mirror to targetIndex for any legacy references
    let targetIndex = (typeof window.targetIndex === 'number') ? window.targetIndex : outputIndex;
    
    // Add initial row if no items exist
    if (rowIndex === 0) {
        try { console.debug('[semi-finished-usage-requests-form] no rows found, auto-adding first row'); } catch (_) {}
        addItemRow();
    }

    // Function to add a new output row (preferred). Falls back to legacy template/body if needed.
    function addOutputRow() {
        // Prefer outputs template
        let tmplEl = document.getElementById('outputRowTemplate');
        let usingLegacyTemplate = false;
        if (!tmplEl) {
            tmplEl = document.getElementById('targetRowTemplate');
            usingLegacyTemplate = !!tmplEl;
            if (!tmplEl) {
                try { console.error('[semi-finished-usage-requests-form] #outputRowTemplate/#targetRowTemplate not found'); } catch (_) {}
                return;
            }
        }
        const template = tmplEl.innerHTML;
        const newRow = template.replace(/__index__/g, outputIndex);
        // Prefer outputs tbody
        let $tbody = $('#outputsTableBody');
        if ($tbody.length === 0) {
            $tbody = $('#targetsTableBody');
        }
        if ($tbody.length === 0) {
            try { console.error('[semi-finished-usage-requests-form] #outputsTableBody/#targetsTableBody not found'); } catch (_) {}
            return;
        }
        $tbody.append(newRow);
        try { console.debug('[semi-finished-usage-requests-form] added output row index', outputIndex, 'legacyTemplate=', usingLegacyTemplate); } catch (_) {}
        // Initialize unit info for the appended row
        try {
            const $lastRow = ($tbody.is('#outputsTableBody') ? $('#outputsTableBody .output-row').last() : $('#targetsTableBody .target-row').last());
            updateOutputUnitForRow($lastRow);
        } catch (_) {}
        outputIndex++;
        targetIndex = outputIndex; // keep in sync for legacy
        try { window.outputIndex = outputIndex; window.targetIndex = targetIndex; } catch (_) {}
    }
    // Backward-compat alias
    function addTargetRow() { return addOutputRow(); }
    
    // Add item row button
    $("#addItemBtn").on('click', function() {
        addItemRow();
    });
    
    // Add output row buttons (new + legacy)
    $("#addOutputBtn").on('click', function() { addOutputRow(); });
    $("#addTargetBtn").on('click', function() { addTargetRow(); });
    
    // Remove item row
    $(document).on('click', '.remove-item', function() {
        // Only remove if there's more than one row
        if ($('.item-row').length > 1) {
            $(this).closest('tr').remove();
        } else {
            showAlert('warning', 'Minimal harus ada satu item bahan');
        }
    });
    
    // Remove output/target row (optional, allow removing all)
    $(document).on('click', '.remove-output, .remove-target', function() {
        $(this).closest('tr').remove();
    });

    // ================= Output Unit Display (with legacy support) =================
    function updateOutputUnitForRow($row) {
        if (!$row || $row.length === 0) return;
        const $select = $row.find('.output-product-select, .target-product-select');
        const $info = $row.find('.output-unit-info, .target-unit-info');
        if ($info.length === 0) return;
        const $opt = $select.find(':selected');
        const unitName = ($opt.data('unit') || '').toString();
        const unitAbbr = ($opt.data('unit-abbr') || '').toString();
        const text = unitName
            ? (unitAbbr ? `Satuan: ${unitName} (${unitAbbr})` : `Satuan: ${unitName}`)
            : '';
        $info.text(text);
    }
    // Legacy alias
    function updateTargetUnitForRow($row) { return updateOutputUnitForRow($row); }

    // Delegated change handler to update unit text when product changes
    $(document).on('change', '.output-product-select, .target-product-select', function() {
        updateOutputUnitForRow($(this).closest('tr'));
    });
    
    // Enforce integer-only input for output/target planned quantities
    $(document).on('input', '.output-qty-input, .target-qty-input', function() {
        const digitsOnly = this.value.replace(/[^0-9]/g, '');
        if (this.value !== digitsOnly) this.value = digitsOnly;
    });

    // Auto-select unit when material is selected
    $(document).on('change', '.raw-material-select', function() {
        const unitId = $(this).find(':selected').data('unit-id');
        if (unitId) {
            $(this).closest('tr').find('.unit-select').val(unitId);
        }

        // Check stock levels and show warning if low
        const selectedOption = $(this).find(':selected');
        const materialName = selectedOption.text().split('(Stok:')[0].trim();
        const stockText = selectedOption.text().match(/\(Stok: ([\d.,]+)/);
        
        if (stockText && stockText[1]) {
            // Strip thousand separators and any non-digits, then parse as integer
            const normalized = stockText[1].replace(/[^\d-]/g, '');
            const stock = parseInt(normalized, 10);
            if (stock < 5) {
                showStockWarning(materialName, stock);
            }
        }
    });
    
    // Form validation before submit
    $('#materialRequestForm').on('submit', function(e) {
        if ($('.item-row').length === 0) {
            e.preventDefault();
            showAlert('danger', 'Minimal harus ada satu item bahan');
            return false;
        }
        
        let isValid = true;
        $('.raw-material-select').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showAlert('danger', 'Mohon lengkapi semua bahan yang diminta');
            return false;
        }
        
        // Check for duplicate materials
        const materialIds = [];
        let hasDuplicates = false;
        
        $('.raw-material-select').each(function() {
            const materialId = $(this).val();
            if (materialId && materialIds.includes(materialId)) {
                hasDuplicates = true;
                $(this).addClass('is-invalid');
            } else {
                materialIds.push(materialId);
            }
        });
        
        if (hasDuplicates) {
            e.preventDefault();
            showAlert('warning', 'Terdapat bahan yang duplikat. Mohon gabungkan menjadi satu item.');
            return false;
        }
        
        // ================= Validate Output Rows (with legacy support) =================
        const $outputsBody = $('#outputsTableBody');
        const $targetsBody = $('#targetsTableBody');
        // Remove entirely empty rows to avoid server-side required_with triggers
        const $rowsForCleanup = $outputsBody.length ? $outputsBody.find('.output-row') : $targetsBody.find('.target-row');
        $rowsForCleanup.each(function() {
            const $row = $(this);
            const productId = ($row.find('.output-product-select, .target-product-select').val() || '').toString();
            const qty = ($row.find('.output-qty-input, .target-qty-input').val() || '').toString();
            const notes = ($row.find('input[name$="[notes]"]').val() || '').toString();
            if (!productId && !qty && !notes) {
                $row.remove();
            }
        });

        const $outputRows = $outputsBody.length ? $outputsBody.find('.output-row') : $targetsBody.find('.target-row');
        if ($outputRows.length > 0) {
            let outputsValid = true;

            // Required fields per row
            $outputRows.each(function() {
                const $row = $(this);
                const $productSelect = $row.find('.output-product-select, .target-product-select');
                const $qtyInput = $row.find('.output-qty-input, .target-qty-input');
                const productId = $productSelect.val();
                const qtyStr = ($qtyInput.val() || '').toString().trim();
                const isInteger = /^\d+$/.test(qtyStr);
                const qtyInt = isInteger ? parseInt(qtyStr, 10) : NaN;

                if (!productId) {
                    outputsValid = false;
                    $productSelect.addClass('is-invalid');
                } else {
                    $productSelect.removeClass('is-invalid');
                }
                if (!isInteger || !qtyInt || qtyInt < 1) {
                    outputsValid = false;
                    $qtyInput.addClass('is-invalid');
                } else {
                    $qtyInput.removeClass('is-invalid');
                }
            });

            if (!outputsValid) {
                e.preventDefault();
                showAlert('danger', 'Mohon lengkapi semua baris Output (produk jadi) yang diisi.');
                return false;
            }

            // Duplicate finished product check
            const fpIds = [];
            let fpDup = false;
            $outputRows.each(function() {
                const pid = $(this).find('.output-product-select, .target-product-select').val();
                if (pid && fpIds.includes(pid)) {
                    fpDup = true;
                    $(this).find('.output-product-select, .target-product-select').addClass('is-invalid');
                } else if (pid) {
                    fpIds.push(pid);
                }
            });
            if (fpDup) {
                e.preventDefault();
                showAlert('warning', 'Terdapat Output dengan produk jadi yang duplikat. Mohon gabungkan menjadi satu baris.');
                return false;
            }
        }

        return true;
    });
    
    // Function to add a new item row
    function addItemRow() {
        const tmplEl = document.getElementById('itemRowTemplate');
        if (!tmplEl) {
            try { console.error('[semi-finished-usage-requests-form] #itemRowTemplate not found'); } catch (_) {}
            return;
        }
        const template = tmplEl.innerHTML;
        const newRow = template.replace(/__index__/g, rowIndex);
        const tbody = document.getElementById('itemsTableBody');
        if (!tbody) {
            try { console.error('[semi-finished-usage-requests-form] #itemsTableBody not found'); } catch (_) {}
            return;
        }
        $('#itemsTableBody').append(newRow);
        try { console.debug('[semi-finished-usage-requests-form] added row index', rowIndex); } catch (_) {}
        rowIndex++;
        // Update global for visibility when testing
        try { window.rowIndex = rowIndex; } catch (_) {}
    }
    
    // Function to show stock warning for low stock materials
    function showStockWarning(materialName, stock) {
        const alertHTML = `
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Perhatian!</strong> Stok ${materialName} sangat sedikit (${stock}). 
                Pastikan ketersediaan sebelum mengajukan permintaan.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Remove any existing alert for this material
        $(".alert").each(function() {
            if ($(this).text().includes(materialName)) {
                $(this).remove();
            }
        });
        
        // Add the new alert at the top of the form
        $('#materialRequestForm').prepend(alertHTML);
    }
    
    // Function to show general alerts
    function showAlert(type, message) {
        const alertHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Remove any similar alerts
        $(".alert").each(function() {
            if ($(this).text().includes(message)) {
                $(this).remove();
            }
        });
        
        // Add the new alert at the top of the form
        $('#materialRequestForm').prepend(alertHTML);
    }

    // Expose function for manual testing in console
    try { window.addItemRow = addItemRow; } catch (_) {}
    try { window.addOutputRow = addOutputRow; window.addTargetRow = addTargetRow; } catch (_) {}

    // Initialize unit info for any pre-rendered rows (edit pages)
    try {
        if ($('#outputsTableBody').length) {
            $('#outputsTableBody .output-row').each(function(){ updateOutputUnitForRow($(this)); });
        } else {
            $('#targetsTableBody .target-row').each(function(){ updateOutputUnitForRow($(this)); });
        }
    } catch (_) {}
});
