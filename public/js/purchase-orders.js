/**
 * Purchase Orders JavaScript - Clean Implementation
 * Fixes all functionality issues and provides clean, maintainable code
 */

class PurchaseOrderManager {
    constructor() {
        // State management
        this.currentSupplierId = null;
        this.allMaterials = [];
        this.filteredMaterials = [];
        this.itemCounter = 0;
        
        // DOM elements
        this.elements = {
            supplierSelect: document.getElementById('supplier_id'),
            supplierPhoneInput: document.getElementById('supplier_phone'),
            supplierInfo: document.getElementById('supplier-info'),
            addItemBtn: document.getElementById('add-item'),
            validatePricesBtn: document.getElementById('validate-prices'),
            itemsTableBody: document.getElementById('item-rows'),
            itemTemplate: document.getElementById('item-template'),
            totalDisplay: document.getElementById('grand-total'),
            form: document.getElementById('purchase-order-form'),
            draftButton: document.querySelector('button[value="save_draft"]'),
            orderButton: document.querySelector('button[value="order_now"]')
        };
        
        this.init();
    }

    // Initialize existing rows on page load (especially on edit page)
    initializeExistingRows() {
        if (!this.elements.itemsTableBody) return;
        const rows = this.elements.itemsTableBody.querySelectorAll('.item-row');
        if (!rows.length) return;

        rows.forEach(row => {
            const materialSelect = row.querySelector('.raw-material-select');
            const currentValue = materialSelect ? materialSelect.value : null;

            // Populate options for the select to ensure dataset attributes exist
            if (materialSelect) {
                this.populateMaterialSelect(materialSelect);
                if (currentValue) {
                    materialSelect.value = currentValue;
                }
            }

            // Bind events and trigger change to update unit cell (but avoid price override in handler)
            this.bindItemRowEvents(row);

            // Ensure unit cell is set based on current selection
            if (materialSelect && materialSelect.value) {
                const unitCell = row.querySelector('.unit-name');
                const material = this.filteredMaterials.find(m => m.id == materialSelect.value);
                if (unitCell) {
                    const unitName = (material && material.unit) ? (material.unit.unit_name || material.unit.name) : '-';
                    unitCell.textContent = unitName || '-';
                }
            }
        });
    }
    
    init() {
        console.log('🚀 Initializing Purchase Order Manager...');
        
        // Load materials from window object (passed from Blade template)
        this.allMaterials = window.rawMaterials || [];
        console.log(`📦 Loaded ${this.allMaterials.length} raw materials`);
        
        // Debug: Show first few materials and their supplier_id
        if (this.allMaterials.length > 0) {
            console.log('🔍 DEBUG: First 3 raw materials:', this.allMaterials.slice(0, 3));
            console.log('🔍 DEBUG: Supplier IDs found:', [...new Set(this.allMaterials.map(m => m.supplier_id))]);
        } else {
            console.warn('⚠️ WARNING: No raw materials loaded from backend!');
        }
        
        // Initialize supplier state if already selected (e.g., on edit page)
        if (this.elements.supplierSelect && this.elements.supplierSelect.value) {
            this.currentSupplierId = this.elements.supplierSelect.value;
            // Pre-filter materials and update info to enable buttons
            this.filterMaterialsBySupplier(this.currentSupplierId);
            this.updateSupplierInfo(this.currentSupplierId);
            // Background refresh latest materials and refresh row options without resetting items
            this.reloadMaterialsData(this.currentSupplierId)
                .then(() => {
                    this.filterMaterialsBySupplier(this.currentSupplierId);
                    this.refreshRowSelectOptions();
                })
                .catch((err) => {
                    console.warn('⚠️ Failed to background-refresh materials on init:', err);
                });
        }

        // Initialize itemCounter based on existing rows (important for edit page)
        if (this.elements.itemsTableBody) {
            const rows = this.elements.itemsTableBody.querySelectorAll('.item-row');
            let maxIndex = -1;
            rows.forEach((row) => {
                // Try to read index from any field name like items[<index>][...]
                const anyField = row.querySelector('[name^="items["]');
                if (anyField && anyField.name) {
                    const match = anyField.name.match(/items\[(\d+)\]/);
                    if (match) {
                        const idx = parseInt(match[1], 10);
                        if (!isNaN(idx)) {
                            maxIndex = Math.max(maxIndex, idx);
                        }
                    }
                }
            });
            this.itemCounter = maxIndex + 1;
        }

        // Initialize existing rows (important for edit page)
        this.initializeExistingRows();

        this.bindEvents();
        this.updateUIState();
        
        console.log('✅ Purchase Order Manager initialized successfully');
    }
    
    bindEvents() {
        // Supplier selection change
        if (this.elements.supplierSelect) {
            this.elements.supplierSelect.addEventListener('change', (e) => {
                this.handleSupplierChange(e.target.value);
            });
        }
        
        // Add item button
        if (this.elements.addItemBtn) {
            this.elements.addItemBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.addNewItem();
            });
        }
        
        // Validate prices button
        if (this.elements.validatePricesBtn) {
            this.elements.validatePricesBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.validatePrices();
            });
        }
        
        // Form submission - add form submit event instead of button clicks
        if (this.elements.form) {
            this.elements.form.addEventListener('submit', (e) => {
                console.log('🔄 Form submit event triggered');
                console.log('Submit button:', e.submitter);
                console.log('Submit action:', e.submitter ? e.submitter.value : 'not supported');
                
                const submitAction = e.submitter ? e.submitter.value : null;
                if (!submitAction) {
                    console.error('❌ Browser tidak mendukung e.submitter, menggunakan solusi alternatif');
                    // Try to determine which button was clicked by checking which one has focus
                    const draftButton = document.querySelector('button[value="save_draft"]');
                    const orderButton = document.querySelector('button[value="order_now"]');
                    
                    if (document.activeElement === draftButton) {
                        console.log('Using activeElement fallback: save_draft');
                        this.handleDraftSubmit(e);
                    } else if (document.activeElement === orderButton) {
                        console.log('Using activeElement fallback: order_now');
                        this.handleOrderSubmit(e);
                    } else {
                        console.log('Could not determine which button was clicked, defaulting to draft');
                        this.handleDraftSubmit(e);
                    }
                    return;
                }
                
                if (submitAction === 'save_draft') {
                    console.log('🔄 Draft submission detected');
                    this.handleDraftSubmit(e);
                } else if (submitAction === 'order_now') {
                    console.log('🔄 Order submission detected');
                    this.handleOrderSubmit(e);
                }
            });
        }
    }
    
    // Handle draft submission
    handleDraftSubmit(e) {
        // For drafts, don't prevent default, just validate
        if (!this.validateForm()) {
            console.log('❌ Form validation failed for draft');
            e.preventDefault();
        } else {
            console.log('✅ Draft form validated, submitting...');
        }
    }
    
    // Handle order submission
    handleOrderSubmit(e) {
        // For orders, show confirmation and then submit manually
        e.preventDefault();
        console.log('🔄 Order submission - showing confirmation');
        this.submitOrder();
    }
    
    handleSupplierChange(newSupplierId) {
        console.log(`🔄 Supplier changed to: ${newSupplierId}`);
        
        // Check if there are existing items and show warning
        const existingItems = this.elements.itemsTableBody?.children.length || 0;
        const hasNonEmptyRows = Array.from(this.elements.itemsTableBody?.children || [])
            .some(row => !row.classList.contains('empty-row'));
        
        if (this.currentSupplierId && 
            this.currentSupplierId !== newSupplierId && 
            hasNonEmptyRows) {
            
            this.showSupplierChangeWarning(newSupplierId);
            return;
        }
        
        this.applySupplierChange(newSupplierId);
    }
    
    showSupplierChangeWarning(newSupplierId) {
        Swal.fire({
            title: '⚠️ Konfirmasi Perubahan Supplier',
            text: 'Mengganti supplier akan menghapus semua item yang sudah ditambahkan. Lanjutkan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Ganti Supplier',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                this.applySupplierChange(newSupplierId);
            } else {
                // Reset supplier selection to previous value
                this.elements.supplierSelect.value = this.currentSupplierId || '';
            }
        });
    }
    
    applySupplierChange(supplierId) {
        this.currentSupplierId = supplierId;
        
        // If supplier cleared, reset UI immediately
        if (!supplierId) {
            this.filteredMaterials = [];
            this.updateSupplierInfo(null);
            this.resetAllItems();
            this.updateUIState();
            if (window.Swal) Swal.close();
            return;
        }
        
        // Show loading while fetching latest materials
        this.showLoadingState('Mengambil data bahan mentah terbaru...');
        
        // Always refresh from server to ensure latest prices/materials
        this.reloadMaterialsData(supplierId)
            .then(() => {
                // Filter with latest data
                this.filterMaterialsBySupplier(supplierId);
                // Update supplier info and phone (now has correct material count)
                this.updateSupplierInfo(supplierId);
                // Reset all items
                this.resetAllItems();
                // Update UI state
                this.updateUIState();
                // Close loading dialog
                if (window.Swal) Swal.close();
            })
            .catch((error) => {
                console.error('❌ Failed to reload materials, using local cache:', error);
                // Fallback to local filter
                this.filterMaterialsBySupplier(supplierId);
                this.updateSupplierInfo(supplierId);
                this.resetAllItems();
                this.updateUIState();
                if (window.Swal) Swal.close();
            });
    }
    
    updateSupplierInfo(supplierId) {
        const supplierOption = this.elements.supplierSelect?.selectedOptions[0];
        
        if (supplierId && supplierOption) {
            // Update phone field
            const phone = supplierOption.dataset.phone || '';
            if (this.elements.supplierPhoneInput) {
                this.elements.supplierPhoneInput.value = phone;
            }
            
            // Update info message
            const supplierName = supplierOption.textContent.trim();
            const materialCount = this.filteredMaterials.length;
            
            if (this.elements.supplierInfo) {
                this.elements.supplierInfo.innerHTML = `
                    <i class="bi bi-check-circle me-1"></i> 
                    Supplier dipilih: <strong>${supplierName}</strong> 
                    <span class="badge bg-success">${materialCount} bahan mentah tersedia</span>
                    <br><small>Klik "Tambah Bahan Mentah" untuk menambah item pesanan.</small>
                `;
                this.elements.supplierInfo.className = 'alert alert-success';
            }
        } else {
            // Reset to default state
            if (this.elements.supplierPhoneInput) {
                this.elements.supplierPhoneInput.value = '';
            }
            
            if (this.elements.supplierInfo) {
                this.elements.supplierInfo.innerHTML = `
                    <i class="bi bi-info-circle me-1"></i> 
                    Pilih supplier terlebih dahulu untuk melihat bahan mentah yang tersedia.
                    <br><small><strong>Catatan:</strong> Jika supplier diganti, semua item akan direset.</small>
                `;
                this.elements.supplierInfo.className = 'alert alert-info';
            }
        }
    }
    
    filterMaterialsBySupplier(supplierId) {
        // Reset filteredMaterials
        this.filteredMaterials = [];
        
        if (this.allMaterials.length > 0) {
            // Filter materials by supplier locally if we have them already
            this.filteredMaterials = this.allMaterials.filter(m => m.supplier_id == supplierId);
            console.log(`🔍 Filtered ${this.filteredMaterials.length} materials for supplier ${supplierId}`);
            
            if (this.filteredMaterials.length === 0) {
                console.warn(`⚠️ WARNING: No materials found for supplier ${supplierId}`);
                console.log('🔍 DEBUG: Available supplier IDs in materials:', [...new Set(this.allMaterials.map(m => m.supplier_id))]);
            }
        } else {
            console.log('❌ No materials data available to filter');
        }
        
        return this.filteredMaterials;
    }
    
    reloadMaterialsData(supplierId) {
        return new Promise((resolve, reject) => {
            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (!token) {
                console.error('❌ CSRF token not found');
                reject('CSRF token not found');
                return;
            }
            
            // Make AJAX request to get latest materials data
            fetch(`/get-materials-by-supplier?supplier_id=${supplierId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ Reloaded materials data:', data);
                
                // Handle different response formats
                let materialsArray = [];
                let foundArrayFormat = false;
                
                // Check if data is directly an array
                if (Array.isArray(data)) {
                    materialsArray = data;
                    foundArrayFormat = true;
                } 
                // Check if data is an object with data property that's an array
                else if (data && typeof data === 'object' && Array.isArray(data.data)) {
                    materialsArray = data.data;
                    foundArrayFormat = true;
                } 
                // Check if data is an object with materials property that's an array
                else if (data && typeof data === 'object' && Array.isArray(data.materials)) {
                    materialsArray = data.materials;
                    foundArrayFormat = true;
                }
                // Check if data is an object where values form an array of materials
                else if (data && typeof data === 'object') {
                    // Try to extract values from object (only if they look like material objects)
                    const possibleArray = Object.values(data);
                    if (Array.isArray(possibleArray) && possibleArray.length > 0) {
                        const looksLikeMaterials = possibleArray.every(v => v && typeof v === 'object' && Object.prototype.hasOwnProperty.call(v, 'id'));
                        if (looksLikeMaterials) {
                            materialsArray = possibleArray;
                            foundArrayFormat = true;
                        }
                    }
                }
                
                // If we found an array format (even if empty), update our data
                if (foundArrayFormat) {
                    console.log('✅ Extracted materials array:', materialsArray.length, 'items');
                    this.filteredMaterials = materialsArray;
                    
                    // Merge into allMaterials: update if exists, push if new
                    materialsArray.forEach(newMaterial => {
                        if (!newMaterial) return; // Skip if null/undefined
                        const index = this.allMaterials.findIndex(m => m && m.id === newMaterial.id);
                        if (index !== -1) {
                            // Update existing material
                            this.allMaterials[index] = newMaterial;
                        } else {
                            this.allMaterials.push(newMaterial);
                        }
                    });
                    
                    resolve(materialsArray);
                } else {
                    console.error('❌ Could not extract materials array from response:', data);
                    reject('Invalid data format received - could not extract materials');
                }
            })
            .catch(error => {
                console.error('❌ Error reloading materials data:', error);
                reject(error);
            });
        });
    }
    
    resetAllItems() {
        if (!this.elements.itemsTableBody) return;
        
        // Clear all existing items and show empty state
        this.elements.itemsTableBody.innerHTML = `
            <tr class="empty-row">
                <td colspan="7" class="text-center py-4">
                    <i class="bi bi-cart-x fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted mb-0">Belum ada item. Klik tombol Tambah Item di bawah.</p>
                </td>
            </tr>
        `;
        
        this.itemCounter = 0;
        this.updateGrandTotal();
    }
    
    addNewItem() {
        if (!this.elements.itemsTableBody || !this.elements.itemTemplate) {
            console.error('❌ Missing required DOM elements for adding items');
            return;
        }
        
        // Check if supplier is selected first (requirement #1)
        if (!this.currentSupplierId) {
            Swal.fire({
                title: 'Supplier Diperlukan',
                text: 'Silakan pilih supplier terlebih dahulu sebelum menambahkan item.',
                icon: 'warning',
                confirmButtonColor: '#ea580c'
            });
            return;
        }
        
        // Check if we have materials for this supplier
        if (this.filteredMaterials.length === 0) {
            // Try to filter materials one more time
            this.filterMaterialsBySupplier(this.currentSupplierId);
            
            // If still empty, show warning
            if (this.filteredMaterials.length === 0) {
                Swal.fire({
                    title: 'Tidak Ada Bahan Mentah',
                    text: 'Tidak ada bahan mentah yang terdaftar untuk supplier ini.',
                    icon: 'info',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }
        }
        
        try {
            // Remove empty row if exists
            const emptyRow = this.elements.itemsTableBody.querySelector('.empty-row');
            if (emptyRow) {
                emptyRow.remove();
            }
            
            // Get template HTML and replace index placeholder
            const templateHTML = this.elements.itemTemplate.innerHTML.replace(/__index__/g, this.itemCounter);
            
            // Create new row directly using innerHTML
            this.elements.itemsTableBody.insertAdjacentHTML('beforeend', templateHTML);
            
            // Get the newly added row
            const newRow = this.elements.itemsTableBody.lastElementChild;
            
            // Populate material select in the new row
            const materialSelect = newRow.querySelector('.raw-material-select');
            if (materialSelect) {
                // Populate the dropdown with filtered materials from the selected supplier
                this.populateMaterialSelect(materialSelect);
            }
            
            // Update row number
            this.updateRowNumbers();
            
            // Bind events for new row
            this.bindItemRowEvents(newRow);
            
            this.itemCounter++;
            
            console.log('✅ Added new item row');
            
        } catch (error) {
            console.error('❌ Error adding new item:', error);
            Swal.fire({
                title: 'Error',
                text: 'Gagal menambah item baru: ' + error.message,
                icon: 'error',
                confirmButtonColor: '#ea580c'
            });
        }
    }
    
    populateMaterialSelect(selectElement) {
        if (!selectElement) {
            console.error('❌ ERROR: selectElement is null/undefined');
            return;
        }
        
        // Clear and add default option
        selectElement.innerHTML = '<option value="">-- Pilih Bahan Mentah --</option>';
        
        // Add filtered materials
        this.filteredMaterials.forEach(material => {
            const option = document.createElement('option');
            option.value = material.id;
            option.textContent = `${material.name} (${material.code})`;
            
            // Debug the unit information
            console.log(`Material ${material.id} (${material.name}) unit data:`, material.unit);
            
            // Store unit name explicitly for proper display
            if (material.unit && material.unit.unit_name) {
                option.dataset.unit = material.unit.unit_name;
            } else if (material.unit && material.unit.name) {
                option.dataset.unit = material.unit.name;
            } else {
                option.dataset.unit = '-';
            }
            
            option.dataset.price = String(Math.round(Number(material.unit_price || 0)));
            option.dataset.stock = material.current_stock || 0;
            
            selectElement.appendChild(option);
        });
    }
    
    refreshRowSelectOptions() {
        if (!this.elements.itemsTableBody) return;
        const rows = this.elements.itemsTableBody.querySelectorAll('.item-row');
        rows.forEach(row => {
            const materialSelect = row.querySelector('.raw-material-select');
            if (materialSelect) {
                const currentValue = materialSelect.value;
                this.populateMaterialSelect(materialSelect);
                if (currentValue) {
                    materialSelect.value = currentValue;
                    // Update unit cell to reflect potentially refreshed unit data
                    const unitCell = row.querySelector('.unit-name');
                    if (unitCell) {
                        let unitName = materialSelect.selectedOptions[0]?.dataset.unit;
                        if (!unitName || unitName === '-') {
                            const mat = this.filteredMaterials.find(m => m.id == currentValue);
                            if (mat && mat.unit) {
                                unitName = mat.unit.unit_name || mat.unit.name || '-';
                            }
                        }
                        unitCell.textContent = unitName || '-';
                    }
                }
            }
        });
    }
    
    bindItemRowEvents(row) {
        // Material selection change
        const materialSelect = row.querySelector('.raw-material-select');
        if (materialSelect) {
            materialSelect.addEventListener('change', (e) => {
                this.handleMaterialChange(e, row);
            });
            
            // Trigger change event if a value is already selected (for re-initialization)
            if (materialSelect.value) {
                const event = new Event('change');
                materialSelect.dispatchEvent(event);
            }

            // Populate options on focus while preserving current selection (useful on edit page)
            materialSelect.addEventListener('focus', () => {
                const currentValue = materialSelect.value;
                // Only repopulate if options look incomplete (e.g., only 1 selected option)
                if (materialSelect.options.length <= 1) {
                    this.populateMaterialSelect(materialSelect);
                    if (currentValue) {
                        materialSelect.value = currentValue;
                    }
                }
            });
        }
        
        // Quantity and price changes
        const quantityInput = row.querySelector('.item-quantity');
        const priceInput = row.querySelector('.item-price');
        
        // Enforce integer-only attributes and sanitize current values
        if (quantityInput) {
            quantityInput.setAttribute('step', '1');
            quantityInput.setAttribute('min', '0');
            quantityInput.setAttribute('inputmode', 'numeric');
            quantityInput.setAttribute('pattern', '[0-9]*');
            quantityInput.value = String(Math.round(parseFloat(quantityInput.value) || 0));
        }
        if (priceInput) {
            priceInput.setAttribute('step', '1');
            priceInput.setAttribute('min', '0');
            priceInput.setAttribute('inputmode', 'numeric');
            priceInput.setAttribute('pattern', '[0-9]*');
            priceInput.value = String(Math.round(parseFloat(priceInput.value) || 0));
        }
        
        [quantityInput, priceInput].forEach(input => {
            if (input) {
                input.addEventListener('input', () => {
                    this.updateRowTotal(row);
                });
            }
        });
        
        // Remove button
        const removeBtn = row.querySelector('.remove-item');
        if (removeBtn) {
            removeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.removeItem(row);
            });
        }
    }
    
    handleMaterialChange(event, row) {
        const selectedOption = event.target.selectedOptions[0];
        
        if (selectedOption && selectedOption.value) {
            // Get the material ID
            const materialId = selectedOption.value;
            
            // Find the material data from our filtered materials
            const selectedMaterial = this.filteredMaterials.find(m => m.id == materialId);
            
            // Update unit display - use multiple sources to ensure reliability
            const unitCell = row.querySelector('.unit-name');
            if (unitCell) {
                // First try to get unit from data attribute
                let unitName = selectedOption.dataset.unit;
                
                // If that's empty or placeholder, try to get it from the material object
                if (!unitName || unitName === '-') {
                    if (selectedMaterial && selectedMaterial.unit) {
                        unitName = selectedMaterial.unit.unit_name || selectedMaterial.unit.name || '-';
                    } else {
                        unitName = '-';
                    }
                }
                
                // Update the display
                unitCell.textContent = unitName || '-';
                console.log(`Updated unit cell to: ${unitCell.textContent}`);
            }
            
            // Update price
            const priceInput = row.querySelector('.item-price');
            if (priceInput) {
                // Only update price if data-price exists on option
                const hasDataPrice = selectedOption.hasAttribute('data-price');
                if (hasDataPrice) {
                    // If this is a user-triggered change, update price directly
                    // If programmatic (initialization), only set price when empty to avoid overwriting existing values
                    if (event.isTrusted) {
                        priceInput.value = String(Math.round(Number(selectedOption.dataset.price || 0)));
                    } else if (priceInput.value === '' || priceInput.value === null) {
                        priceInput.value = String(Math.round(Number(selectedOption.dataset.price || 0)));
                    }
                }
            }
            
            // Update row total
            this.updateRowTotal(row);
        }
    }
    
    removeItem(row) {
        Swal.fire({
            title: 'Hapus Item',
            text: 'Yakin ingin menghapus item ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                row.remove();
                this.updateRowNumbers();
                this.updateGrandTotal();
                
                // Show empty state if no items left
                if (this.elements.itemsTableBody.children.length === 0) {
                    this.resetAllItems();
                }
            }
        });
    }
    
    updateRowNumbers() {
        const rows = this.elements.itemsTableBody.querySelectorAll('.item-row');
        rows.forEach((row, index) => {
            const numberCell = row.querySelector('.row-number');
            if (numberCell) {
                numberCell.textContent = index + 1;
            }
        });
    }
    
    updateRowTotal(row) {
        // Sanitize to integers
        const quantityInput = row.querySelector('.item-quantity');
        const priceInput = row.querySelector('.item-price');
        let quantity = Math.round(parseFloat(quantityInput?.value) || 0);
        let price = Math.round(parseFloat(priceInput?.value) || 0);

        if (quantityInput) quantityInput.value = String(quantity);
        if (priceInput) priceInput.value = String(price);

        const total = quantity * price;
        
        const totalCell = row.querySelector('.item-total');
        if (totalCell) {
            totalCell.textContent = this.formatCurrency(total);
        }
        
        // Update grand total
        this.updateGrandTotal();
    }
    
    updateGrandTotal() {
        const rows = this.elements.itemsTableBody.querySelectorAll('.item-row');
        let grandTotal = 0;
        
        rows.forEach(row => {
            const quantity = Math.round(parseFloat(row.querySelector('.item-quantity')?.value) || 0);
            const price = Math.round(parseFloat(row.querySelector('.item-price')?.value) || 0);
            grandTotal += (quantity * price);
        });
        
        if (this.elements.totalDisplay) {
            this.elements.totalDisplay.textContent = this.formatCurrency(grandTotal);
        }
    }
    
    validatePrices() {
        if (!this.currentSupplierId) {
            Swal.fire({
                title: 'Supplier Diperlukan',
                text: 'Pilih supplier terlebih dahulu sebelum memvalidasi harga.',
                icon: 'warning',
                confirmButtonColor: '#ea580c'
            });
            return;
        }

        // Check if there are any items to validate
        const rows = this.elements.itemsTableBody.querySelectorAll('.item-row');
        if (!rows.length) {
            Swal.fire({
                title: 'Tidak Ada Item',
                text: 'Tambahkan item terlebih dahulu sebelum memvalidasi harga.',
                icon: 'info',
                confirmButtonColor: '#ea580c'
            });
            return;
        }
        
        // Show loading state
        const loadingDialog = Swal.fire({
            title: 'Memvalidasi Harga',
            text: 'Sedang memperbarui data harga terbaru...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Reload materials data from server to get latest prices
        this.reloadMaterialsData(this.currentSupplierId)
            .then(materials => {
                console.log('✅ Successfully loaded materials for price validation:', materials.length);
                
                // Update prices in each row while preserving material selection and quantity
                let updatedCount = 0;
                
                rows.forEach(row => {
                    const materialSelect = row.querySelector('.raw-material-select');
                    const priceInput = row.querySelector('.item-price');
                    
                    if (materialSelect && materialSelect.value && priceInput) {
                        const materialId = materialSelect.value;
                        const material = this.filteredMaterials.find(m => m.id == materialId);
                        
                        if (material && material.unit_price) {
                            // Store previous price for comparison (as integer)
                            const previousPrice = Math.round(parseFloat(priceInput.value) || 0);
                            const newPrice = Math.round(parseFloat(material.unit_price) || 0);
                            
                            // Update the price as integer string
                            priceInput.value = String(newPrice);
                            
                            // Highlight the price change with animation
                            if (previousPrice !== newPrice) {
                                priceInput.classList.add('price-updated');
                                setTimeout(() => {
                                    priceInput.classList.remove('price-updated');
                                }, 2000);
                                updatedCount++;
                            }
                            
                            // Update the row total
                            this.updateRowTotal(row);
                        }
                    }
                });
                
                // Update grand total
                this.updateGrandTotal();
                
                // Show success message
                if (window.Swal) Swal.close();
                return Swal.fire({
                    title: 'Harga Diperbarui',
                    text: updatedCount > 0 ? 
                        `Berhasil memperbarui ${updatedCount} harga item dengan data terbaru.` : 
                        'Semua harga sudah terkini.',
                    icon: 'success',
                    confirmButtonColor: '#059669'
                });
            })
            .catch(error => {
                console.error('❌ Error during price validation:', error);
                if (window.Swal) Swal.close();
                return Swal.fire({
                    title: 'Gagal Memperbarui Harga',
                    text: 'Terjadi kesalahan saat memperbarui harga. Silakan coba lagi.',
                    icon: 'error',
                    confirmButtonColor: '#dc2626'
                });
            });
    }
    
    updateUIState() {
        const hasSupplier = !!this.currentSupplierId;
        
        // Add/Remove disabled state for buttons that require supplier
        if (this.elements.validatePricesBtn) {
            this.elements.validatePricesBtn.disabled = !hasSupplier;
        }
        
        if (this.elements.orderButton) {
            this.elements.orderButton.disabled = !hasSupplier;
        }
        
        // Add item button should also require supplier
        if (this.elements.addItemBtn) {
            this.elements.addItemBtn.disabled = !hasSupplier;
        }
    }
    
    handleFormSubmit(event, action) {
        event.preventDefault();
        
        if (!this.validateForm()) {
            return;
        }
        
        if (action === 'draft') {
            this.saveDraft();
        } else if (action === 'order') {
            this.submitOrder();
        }
    }
    
    validateForm() {
        // Check supplier selection
        if (!this.currentSupplierId) {
            this.showErrorMessage('Pilih supplier terlebih dahulu');
            return false;
        }
        
        // Check if there are items
        const itemRows = this.elements.itemsTableBody.querySelectorAll('.item-row');
        if (itemRows.length === 0) {
            this.showErrorMessage('Tambahkan minimal satu item pesanan');
            return false;
        }
        
        // Validate each item
        for (let row of itemRows) {
            const material = row.querySelector('.raw-material-select')?.value;
            const qInput = row.querySelector('.item-quantity');
            const pInput = row.querySelector('.item-price');
            const quantity = Math.round(parseFloat(qInput?.value) || 0);
            const price = Math.round(parseFloat(pInput?.value) || 0);

            // write back sanitized values
            if (qInput) qInput.value = String(quantity);
            if (pInput) pInput.value = String(price);
            
            if (!material || quantity <= 0 || price < 0) {
                this.showErrorMessage('Pastikan semua item memiliki bahan mentah, kuantitas, dan harga yang valid');
                return false;
            }
        }
        
        return true;
    }
    
    saveDraft() {
        console.log('✅ Preparing draft submission');
        // Ensure submit_action=save_draft is included when form.submit() is called
        let actionInput = this.elements.form.querySelector('input[name="submit_action"]');
        if (!actionInput) {
            actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'submit_action';
            this.elements.form.appendChild(actionInput);
        }
        actionInput.value = 'save_draft';
        this.elements.form.submit();
    }
    
    submitOrder() {
        const supplierOption = this.elements.supplierSelect.selectedOptions[0];
        const supplierName = supplierOption?.textContent.trim() || 'Supplier';
        
        Swal.fire({
            title: 'Konfirmasi Pesanan',
            html: `
                <p>Pesanan akan dikirim ke <strong>${supplierName}</strong> melalui WhatsApp.</p>
                <p class="text-muted">Setelah dikonfirmasi, pesanan tidak dapat diubah.</p>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Kirim Pesanan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('✅ User confirmed order, appending submit_action and submitting');
                // Ensure submit_action=order_now is included
                let actionInput = this.elements.form.querySelector('input[name="submit_action"]');
                if (!actionInput) {
                    actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'submit_action';
                    this.elements.form.appendChild(actionInput);
                }
                actionInput.value = 'order_now';
                this.elements.form.submit();
            }
        });
    }
    
    // Utility methods
    
    
    showErrorMessage(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            confirmButtonColor: '#dc2626'
        });
    }
    
    showSuccessMessage(message) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: message,
            confirmButtonColor: '#16a34a'
        });
    }
    
    formatCurrency(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(amount || 0));
    }
    
    showLoadingState(message) {
        Swal.fire({
            title: 'Memproses...',
            text: message,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 DOM Content Loaded - Initializing Purchase Order Manager...');
    
    // Check if we're on the purchase order create page
    if (document.getElementById('purchase-order-form')) {
        window.purchaseOrderManager = new PurchaseOrderManager();
    } else {
        console.log('⚠️ Purchase order form not found - skipping initialization');
    }
});
