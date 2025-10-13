document.addEventListener('DOMContentLoaded', function () {
    const state = {
        products: [],
        packages: [],
        originalProducts: [], // Store original product data for stock calculations
        originalPackages: [], // Store original package data for stock calculations
        cart: [],
        categories: new Set(),
        customer: {
            name: '',
            phone: ''
        },
        payment: {
            method: 'cash',
            amount_paid: 0,
            discount_type: 'nominal',
            discount_value: 0,
        },
        branchId: null,
        loading: true,
        error: null,
        activeFilter: 'all',
        // Stock refresh control
        lastStockRefresh: new Date(),
        stockRefreshInterval: 30000, // Refresh stock every 30 seconds
        refreshingStock: false,
    };

    const selectors = {
        productList: '#product-list',
        categoryFilters: '#category-filters',
        cartItems: '#cart-items',
        subtotal: '#subtotal',
        discountValue: '#discount-value',
        discountType: '#discount-type',
        grandTotal: '#grand-total',
        paymentMethod: 'input[name="payment_method"]',
        amountPaid: '#amount-paid',
        change: '#change',
        checkoutForm: '#checkout-form',
        searchInput: '#search-input',
        customerName: '#customer_name',
        customerPhone: '#customer_phone',
        branchSelector: '#branch_id_selector',
        loadingOverlay: '#loading-overlay',
        errorMessage: '#error-message',
    };

    const elements = {};
    for (const key in selectors) {
        elements[key] = document.querySelector(selectors[key]);
    }

    async function init() {
        setBranchId();
        if (!state.branchId) {
            // Attach early listener so selecting a branch immediately redirects
            if (elements.branchSelector) {
                elements.branchSelector.addEventListener('change', e => {
                    window.location.href = `/sales/create?branch_id=${e.target.value}`;
                });
            }
            handleError('Pilih cabang terlebih dahulu untuk memulai penjualan.');
            return;
        }
        await fetchData();
        render();
        attachEventListeners();
        
        // Apply form locking again after rendering
        toggleFormLocking();
        
        // Setup periodic stock refresh
        setupStockRefresh();
    }
    
    // Function to refresh stock data periodically
    function setupStockRefresh() {
        // Set interval to check and refresh stock data
        setInterval(async () => {
            const now = new Date();
            const timeSinceLastRefresh = now - state.lastStockRefresh;
            
            // Only refresh if enough time has passed and not currently refreshing
            if (timeSinceLastRefresh >= state.stockRefreshInterval && !state.refreshingStock) {
                await refreshStockData();
            }
        }, 5000); // Check every 5 seconds
    }
    
    // Function to refresh just the stock data without full page reload
    async function refreshStockData() {
        if (state.refreshingStock || !state.branchId) return;
        
        state.refreshingStock = true;
        console.log('Refreshing stock data...');
        
        try {
            const [productsRes, packagesRes] = await Promise.all([
                fetch(`/api/sales/products?branch_id=${state.branchId}`),
                fetch(`/api/sales/packages?branch_id=${state.branchId}`)
            ]);

            if (!productsRes.ok || !packagesRes.ok) {
                throw new Error('Failed to refresh stock data');
            }

            const newProductsData = await productsRes.json();
            const newPackagesData = await packagesRes.json();
            
            // Update last refresh time
            state.lastStockRefresh = new Date();
            
            // Update product stock values and mark items with changed stock
            updateProductStockValues(newProductsData, newPackagesData);
            
            // Re-render product list with updated stock values
            renderProductList();
            
            // Update cart items that might have stock changes
            updateCartItemsWithNewStock();
            
            console.log('Stock data refreshed successfully');
        } catch (error) {
            console.error('Error refreshing stock data:', error);
        } finally {
            state.refreshingStock = false;
        }
    }
    
    // Function to update stock values in state with new data
    function updateProductStockValues(newProductsData, newPackagesData) {
        // Update product stock values
        newProductsData = newProductsData.map(p => ({ ...p, type: 'product' }));
        newPackagesData = newPackagesData.map(p => ({ ...p, type: 'package' }));
        
        // Create maps for quick lookup
        const productMap = new Map();
        const packageMap = new Map();
        
        state.products.forEach(p => productMap.set(p.id, p));
        state.packages.forEach(p => packageMap.set(p.id, p));
        
        // Update product stock values
        for (const newProduct of newProductsData) {
            const existingProduct = productMap.get(newProduct.id);
            if (existingProduct) {
                const oldStock = existingProduct.stock;
                const newStock = newProduct.stock;
                
                // Update stock and mark if changed
                existingProduct.stock = newStock;
                existingProduct.stockChanged = oldStock !== newStock;
                
                // If stock is now 0, disable add to cart
                existingProduct.disabled = newStock <= 0;
            }
        }
        
        // Update package stock values
        for (const newPackage of newPackagesData) {
            const existingPackage = packageMap.get(newPackage.id);
            if (existingPackage) {
                const oldStock = existingPackage.calculated_stock;
                const newStock = newPackage.calculated_stock;
                
                // Update stock and mark if changed
                existingPackage.calculated_stock = newStock;
                existingPackage.stockChanged = oldStock !== newStock;
                
                // If stock is now 0, disable add to cart
                existingPackage.disabled = newStock <= 0;
            }
        }
        
        // Add visual feedback for items with changed stock (flash animation)
        setTimeout(() => {
            state.products.forEach(p => p.stockChanged = false);
            state.packages.forEach(p => p.stockChanged = false);
        }, 3000);
    }
    
    // Function to update cart items with new stock information
    function updateCartItemsWithNewStock() {
        const productMap = new Map();
        const packageMap = new Map();
        
        state.products.forEach(p => productMap.set(p.id, p));
        state.packages.forEach(p => packageMap.set(p.id, p));
        
        // Check cart items against current stock
        state.cart.forEach(item => {
            const currentItem = item.type === 'product' 
                ? productMap.get(item.id) 
                : packageMap.get(item.id);
            
            if (currentItem) {
                const currentStock = item.type === 'product' 
                    ? currentItem.stock 
                    : currentItem.calculated_stock;
                
                // If item quantity exceeds available stock, adjust it
                if (item.quantity > currentStock) {
                    // Flash warning
                    const notificationDiv = document.createElement('div');
                    notificationDiv.className = 'alert alert-warning stock-update-alert';
                    notificationDiv.innerHTML = `<i class="bi bi-exclamation-triangle"></i> Stok ${item.name} berkurang! Kuantitas disesuaikan.`;
                    document.querySelector('.container-fluid').prepend(notificationDiv);
                    
                    // Automatically adjust quantity to available stock
                    item.quantity = Math.max(0, currentStock);
                    
                    // Remove notification after 5 seconds
                    setTimeout(() => {
                        notificationDiv.remove();
                    }, 5000);
                }
            }
        });
        
        // Re-render cart to reflect any changes
        renderCart();
    }

    function setBranchId() {
        const urlParams = new URLSearchParams(window.location.search);
        state.branchId = urlParams.get('branch_id') || elements.branchSelector?.value;
        
        // Check if the current branch is a special branch (Pusat Produksi or Overview)
        if (state.branchId) {
            // Fallback: detect branch name from navbar if available
            const navBranchEl = document.querySelector('.navbar .branch-name, .navbar .dropdown-toggle .branch-name');
            if (navBranchEl) {
                const navBranchName = navBranchEl.textContent.trim();
                if (navBranchName) {
                    state.branchName = navBranchName;
                    state.isSpecialBranch = /Pusat Produksi|Overview/i.test(navBranchName);
                }
            }
            
            fetch(`/api/branches/${state.branchId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network res');
                    return response.json();
                })
                .then(branch => {
                    state.isSpecialBranch = /Pusat Produksi|Overview/i.test(branch.name);
                    state.branchName = branch.name;
                    toggleFormLocking();
                })
                .catch(error => {
                    console.warn('Error fetching branch details, applying fallback detection');
                    // Fallback: scan navbar text for keywords
                    const navTextFallback = document.querySelector('nav.navbar')?.innerText || '';
                    if (/Pusat Produksi|Overview/i.test(navTextFallback)) {
                        state.isSpecialBranch = true;
                        const matched = navTextFallback.match(/Pusat Produksi|Overview/i);
                        if (matched) state.branchName = matched[0];
                    }
                    toggleFormLocking();
                });
        }
    }

    async function fetchData() {
        setLoading(true);
        try {
            const [productsRes, packagesRes] = await Promise.all([
                fetch(`/api/sales/products?branch_id=${state.branchId}`),
                fetch(`/api/sales/packages?branch_id=${state.branchId}`)
            ]);

            if (!productsRes.ok || !packagesRes.ok) {
                throw new Error('Gagal memuat data produk atau paket.');
            }

            const productsData = await productsRes.json();
            const packagesData = await packagesRes.json();
            
            // Store last refresh time
            state.lastStockRefresh = new Date();

            // Store products with type information
            state.products = productsData.map(p => ({ ...p, type: 'product' }));
            state.packages = packagesData.map(p => ({ ...p, type: 'package' }));
            
            // Store original data for stock calculations
            state.originalProducts = JSON.parse(JSON.stringify(state.products));
            state.originalPackages = JSON.parse(JSON.stringify(state.packages));

            // Extract categories
            const allItems = [...state.products, ...state.packages];
            allItems.forEach(item => state.categories.add(item.category.name));

        } catch (error) {
            handleError(error.message);
        } finally {
            setLoading(false);
        }
    }

    function render() {
        renderCategoryFilters();
        renderProductList();
        renderCart();
    }

    function renderProductList() {
        const container = elements.productList;
        if (!container) return;
        container.innerHTML = '';

        const searchTerm = elements.searchInput.value.toLowerCase();
        const filteredItems = [...state.products, ...state.packages].filter(item => {
            const matchesCategory = state.activeFilter === 'all' || item.category.name === state.activeFilter;
            const matchesSearch = item.name.toLowerCase().includes(searchTerm);
            return matchesCategory && matchesSearch;
        });

        if (filteredItems.length === 0) {
            container.innerHTML = `<div class="col-12 text-center text-muted py-5"><i class="bi bi-search fs-1"></i><p class="mt-3">Produk tidak ditemukan.</p></div>`;
            return;
        }

        filteredItems.forEach(item => {
            // Make sure stock is always a number, defaulting to 0 if undefined
            const stock = item.type === 'product' ? (item.stock || 0) : (item.calculated_stock || 0);
            const card = document.createElement('div');
            card.className = 'col-md-4 col-lg-3 mb-4';
            const hasPhoto = item.photo && item.photo.trim() !== '';
            
            // Create the card wrapper first
            card.innerHTML = `
                <div class="card product-card h-100 shadow-sm border-0 ${stock <= 0 ? 'out-of-stock' : ''} ${item.stockChanged ? 'stock-changed' : ''}">
                    <div class="product-image-container">
                        ${hasPhoto ? 
                            `<img src="${item.photo}" class="card-img-top product-img p-2" alt="${item.name}" data-product-id="${item.id}" data-product-type="${item.type}">` : 
                            `<div class="d-flex justify-content-center align-items-center bg-light product-img p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-card-image text-secondary" viewBox="0 0 16 16">
                                    <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                    <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54A.5.5 0 0 1 1 12.5v-9a.5.5 0 0 1 .5-.5h13z"/>
                                </svg>
                            </div>`
                        }
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fs-6 fw-bold">${item.name}</h5>
                        <p class="card-text text-primary fw-bold mb-2">Rp ${number_format(item.price)}</p>
                        <p class="card-text small text-muted mb-auto">Stok: ${stock}</p>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-3 add-to-cart-btn" 
                            data-id="${item.id}" 
                            data-type="${item.type}" 
                            ${stock <= 0 ? 'disabled' : ''}>
                            <i class="bi bi-cart-plus"></i> Tambah
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });
        
        // Setup image error handling
        setupImageErrorHandling();
    }
    
    // Handle image load errors and replace with SVG placeholder
    function setupImageErrorHandling() {
        const productImages = document.querySelectorAll('.product-img[src]');
        
        productImages.forEach(img => {
            img.addEventListener('error', function() {
                // Get product details
                const productId = this.getAttribute('data-product-id');
                const productType = this.getAttribute('data-product-type');
                const productName = this.getAttribute('alt');
                
                // Create placeholder SVG
                const placeholder = document.createElement('div');
                placeholder.className = 'd-flex justify-content-center align-items-center bg-light product-img p-2';
                placeholder.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-card-image text-secondary" viewBox="0 0 16 16">
                        <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                        <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54A.5.5 0 0 1 1 12.5v-9a.5.5 0 0 1 .5-.5h13z"/>
                    </svg>
                `;
                
                // Replace the img with the placeholder
                const parent = this.parentNode;
                if (parent) {
                    parent.replaceChild(placeholder, this);
                }
                
                console.log(`Replaced broken image for ${productName} with placeholder`);
            });
        });
    }

    function renderCategoryFilters() {
        const container = elements.categoryFilters;
        if (!container) return;
        container.innerHTML = '';

        [...state.categories].sort().forEach(filter => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `btn btn-sm me-1 mb-1 ${state.activeFilter === filter ? 'btn-primary' : 'btn-outline-primary'}`;
            button.textContent = filter;
            button.dataset.filter = filter;
            container.appendChild(button);
        });
    }

    function renderCart() {
        const container = elements.cartItems;
        if (!container) return;
        container.innerHTML = '';

        if (state.cart.length === 0) {
            container.innerHTML = `<div class="text-center text-muted py-5"><i class="bi bi-cart-x fs-1"></i><p class="mt-3">Keranjang kosong</p></div>`;
        }

        state.cart.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.name}</td>
                <td>
                    <div class="input-group input-group-sm" style="width: 100px;">
                        <button type="button" class="btn btn-outline-secondary quantity-change" data-id="${item.id}" data-type="${item.type}" data-change="-1">-</button>
                        <input type="text" class="form-control text-center quantity-input" value="${item.quantity}" data-id="${item.id}" data-type="${item.type}">
                        <button type="button" class="btn btn-outline-secondary quantity-change" data-id="${item.id}" data-type="${item.type}" data-change="1">+</button>
                    </div>
                </td>
                <td class="text-end">Rp ${number_format(item.price * item.quantity)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-from-cart" data-id="${item.id}" data-type="${item.type}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            container.appendChild(row);
        });

        updateTotals();
    }

    function updateTotals() {
        const subtotal = state.cart.reduce((acc, item) => acc + (Math.round(item.price) * Math.round(item.quantity)), 0);
        const discountAmount = calculateDiscount(subtotal);
        const grandTotal = Math.max(0, subtotal - discountAmount);

        elements.subtotal.textContent = `Rp ${number_format(subtotal)}`;
        elements.grandTotal.textContent = `Rp ${number_format(grandTotal)}`;

        updateChange();
    }

    function calculateDiscount(subtotal) {
        const discountValue = parseFloat(state.payment.discount_value) || 0;
        if (state.payment.discount_type === 'percentage') {
            return Math.round((subtotal * discountValue) / 100);
        }
        return Math.round(discountValue);
    }

    function updateChange() {
        const grandTotal = parseInt(elements.grandTotal.textContent.replace(/[^0-9]/g, ''), 10) || 0;
        const amountPaid = parseInt(state.payment.amount_paid, 10) || 0;
        const change = Math.max(0, amountPaid - grandTotal);
        elements.change.textContent = `Rp ${number_format(change)}`;
    }

    function attachEventListeners() {
        elements.productList.addEventListener('click', e => {
            const btn = e.target.closest('.add-to-cart-btn');
            if (btn) {
                e.preventDefault();
                const { id, type } = btn.dataset;
                addToCart(id, type);
            }
        });

        elements.cartItems.addEventListener('click', e => {
            const button = e.target.closest('button');
            if (!button) return;

            const { id, type } = button.dataset;
            if (button.classList.contains('quantity-change')) {
                const change = parseInt(button.dataset.change, 10);
                updateCartQuantity(id, type, state.cart.find(i => i.id == id && i.type === type).quantity + change);
            } else if (button.classList.contains('remove-from-cart')) {
                removeFromCart(id, type);
            }
        });

        elements.cartItems.addEventListener('change', e => {
            if (e.target.classList.contains('quantity-input')) {
                const { id, type } = e.target.dataset;
                const newQuantity = parseInt(e.target.value, 10);
                updateCartQuantity(id, type, newQuantity);
            }
        });

        elements.categoryFilters.addEventListener('click', e => {
            if (e.target.tagName === 'BUTTON') {
                state.activeFilter = e.target.dataset.filter;
                render();
            }
        });

        elements.searchInput.addEventListener('input', renderProductList);

        elements.discountValue.addEventListener('input', e => {
            state.payment.discount_value = e.target.value;
            updateTotals();
        });

        elements.discountType.addEventListener('change', e => {
            state.payment.discount_type = e.target.value;
            updateTotals();
        });

        elements.amountPaid.addEventListener('input', e => {
            const raw = e.target.value;
            const val = parseInt(String(raw).replace(/[^0-9]/g, ''), 10) || 0;
            state.payment.amount_paid = val;
            updateChange();
        });

        document.querySelectorAll(selectors.paymentMethod).forEach(radio => {
            radio.addEventListener('change', e => {
                state.payment.method = e.target.value;
                elements.amountPaid.disabled = state.payment.method !== 'cash';
            });
        });

        elements.checkoutForm.addEventListener('submit', handleCheckout);

        elements.branchSelector?.addEventListener('change', e => {
            window.location.href = `/sales/create?branch_id=${e.target.value}`;
        });
    }

    function addToCart(id, type) {
        const sourceItem = (type === 'product' ? state.products : state.packages).find(p => p.id == id);
        const cartItem = state.cart.find(item => item.id == id && item.type === type);

        // Get current stock
        const remaining = type === 'product'
            ? (sourceItem?.stock ?? 0)
            : (sourceItem?.calculated_stock ?? sourceItem?.stock ?? 0);
        const quantityInCart = cartItem ? cartItem.quantity : 0;

        // Block only when no remaining stock
        if (remaining <= 0) {
            handleError('Stok tidak mencukupi.');
            return;
        }

        if (cartItem) {
            cartItem.quantity++;
        } else {
            state.cart.push({ ...sourceItem, quantity: 1 });
        }
        
        // Update virtual stock
        updateVirtualStock();
        renderCart();
        renderProductList(); // Re-render products to show updated stock
    }

    function updateCartQuantity(id, type, newQuantity) {
        const cartItem = state.cart.find(item => item.id == id && item.type === type);
        if (!cartItem) return;

        const sourceItem = (type === 'product' ? state.products : state.packages).find(p => p.id == id);
        // Remaining stock available to add on top of current cart quantity
        const remaining = type === 'product'
            ? Math.max(0, sourceItem?.stock ?? 0)
            : Math.max(0, sourceItem?.calculated_stock ?? sourceItem?.stock ?? 0);
        const maxAllowed = (cartItem.quantity || 0) + remaining;

        if (newQuantity > maxAllowed) {
            handleError('Stok tidak mencukupi.');
            cartItem.quantity = maxAllowed;
        } else if (newQuantity <= 0) {
            removeFromCart(id, type);
            return; // removeFromCart already calls renderCart and updateVirtualStock
        } else {
            cartItem.quantity = newQuantity;
        }
        
        // Update virtual stock
        updateVirtualStock();
        renderCart();
        renderProductList(); // Re-render products to show updated stock
    }

    function removeFromCart(id, type) {
        state.cart = state.cart.filter(item => !(item.id == id && item.type === type));
        updateVirtualStock();
        renderCart();
        renderProductList(); // Re-render products to show updated stock
    }
    
    // Update virtual stock based on cart contents
    function updateVirtualStock() {
        // 1) Reset all products to their original stock
        state.products.forEach(product => {
            const originalProduct = state.originalProducts.find(p => p.id === product.id);
            if (originalProduct) {
                product.stock = originalProduct.stock;
            }
        });

        // 2) Reduce product stock based on cart items (products and package components)
        state.cart.forEach(item => {
            if (item.type === 'product') {
                const product = state.products.find(p => p.id === item.id);
                if (product) {
                    product.stock -= item.quantity;
                }
            } else if (item.type === 'package') {
                const pkg = state.packages.find(p => p.id === item.id);
                // Reduce component products' stock according to the package composition
                if (pkg && pkg.components) {
                    pkg.components.forEach(component => {
                        const product = state.products.find(p => p.id === component.id);
                        if (product) {
                            product.stock -= (component.quantity * item.quantity);
                        }
                    });
                }
            }
        });

        // 3) Clamp product stock to never drop below zero
        state.products.forEach(product => {
            product.stock = Math.max(0, Math.round(product.stock));
        });

        // 4) Recompute each package's calculated_stock based on current product stocks
        state.packages.forEach(pkg => {
            const originalPackage = state.originalPackages.find(p => p.id === pkg.id);
            // Compute available packages by components
            if (pkg.components && pkg.components.length > 0) {
                let maxByComponents = Infinity;
                pkg.components.forEach(component => {
                    const product = state.products.find(p => p.id === component.id);
                    if (product && component.quantity > 0) {
                        const possible = Math.floor((product.stock || 0) / component.quantity);
                        maxByComponents = Math.min(maxByComponents, possible);
                    }
                });
                // Limit by original calculated stock if provided by backend
                const originalCalc = originalPackage?.calculated_stock;
                if (typeof originalCalc === 'number') {
                    maxByComponents = Math.min(maxByComponents, originalCalc);
                }
                pkg.calculated_stock = Math.max(0, isFinite(maxByComponents) ? maxByComponents : 0);
            } else {
                // Fallback: keep original calculated_stock if no components info
                pkg.calculated_stock = Math.max(0, originalPackage?.calculated_stock ?? 0);
            }
        });

        // 5) Setup image error handling (idempotent)
        setupImageErrorHandling();
    }
    
    // Handle image load errors and replace with SVG placeholder
    function setupImageErrorHandling() {
        const productImages = document.querySelectorAll('.product-img[src]');
        
        productImages.forEach(img => {
            img.addEventListener('error', function() {
                // Get product details
                const productId = this.getAttribute('data-product-id');
                const productType = this.getAttribute('data-product-type');
                const productName = this.getAttribute('alt');
                
                // Create placeholder SVG
                const placeholder = document.createElement('div');
                placeholder.className = 'd-flex justify-content-center align-items-center bg-light product-img p-2';
                placeholder.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-card-image text-secondary" viewBox="0 0 16 16">
                        <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                        <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54A.5.5 0 0 1 1 12.5v-9a.5.5 0 0 1 .5-.5h13z"/>
                    </svg>
                `;
                
                // Replace the img with the placeholder
                const parent = this.parentNode;
                if (parent) {
                    parent.replaceChild(placeholder, this);
                }
                
                console.log(`Replaced broken image for ${productName} with placeholder`);
            });
        });
    }

    async function handleCheckout(e) {
        e.preventDefault();
        
        // Check if this is a special branch
        if (state.isSpecialBranch) {
            handleError(`Penjualan tidak dapat dilakukan di cabang ${state.branchName}.`);
            return;
        }
        
        // Validate cart
        if (state.cart.length === 0) {
            handleError('Keranjang belanja kosong.');
            return;
        }
        
        // Validate customer information (phone is optional but must be valid if provided)
        const customerName = elements.customerName.value.trim();
        const customerPhone = elements.customerPhone.value.trim();
        
        // Phone format validation - at least 10 digits, allows +
        if (customerPhone && !/^[0-9+]{10,15}$/.test(customerPhone)) {
            handleError('Format nomor telepon tidak valid. Minimal 10 digit angka.');
            elements.customerPhone.classList.add('is-invalid');
            return;
        } else {
            elements.customerPhone.classList.remove('is-invalid');
        }
        
        // Clear customer field validation markers
        elements.customerName.classList.remove('is-invalid');
        
        // Validate discount
        const discountValue = parseFloat(state.payment.discount_value) || 0;
        if (discountValue < 0) {
            handleError('Nilai diskon tidak boleh negatif.');
            elements.discountValue.classList.add('is-invalid');
            return;
        } else if (state.payment.discount_type === 'percentage' && discountValue > 100) {
            handleError('Diskon persentase tidak boleh lebih dari 100%.');
            elements.discountValue.classList.add('is-invalid');
            return;
        } else {
            elements.discountValue.classList.remove('is-invalid');
        }
        
        // Validate the subtotal after discount
        const currentSubtotal = parseFloat(elements.subtotal.textContent.replace(/[^0-9]/g, ''));
        const calculatedDiscount = calculateDiscount(currentSubtotal);
        const totalAfterDiscount = currentSubtotal - calculatedDiscount;
        
        if (totalAfterDiscount < 0) {
            handleError('Total belanja setelah diskon tidak boleh negatif.');
            elements.discountValue.classList.add('is-invalid');
            return;
        }
        
        // Validate payment method is selected
        if (!state.payment.method) {
            handleError('Pilih metode pembayaran.');
            document.querySelectorAll(selectors.paymentMethod).forEach(input => {
                input.classList.add('is-invalid');
            });
            return;
        } else {
            document.querySelectorAll(selectors.paymentMethod).forEach(input => {
                input.classList.remove('is-invalid');
            });
        }
        
        // Get and validate the grand total
        const grandTotal = parseFloat(elements.grandTotal.textContent.replace(/[^0-9]/g, ''));
        if (isNaN(grandTotal) || grandTotal <= 0) {
            handleError('Total belanja tidak valid.');
            return;
        }
        
        // Validate payment based on method
        if (state.payment.method === 'cash') {
            const amountPaid = parseFloat(state.payment.amount_paid) || 0;
            
            if (amountPaid <= 0) {
                handleError('Masukkan jumlah pembayaran tunai.');
                elements.amountPaid.classList.add('is-invalid');
                return;
            } else if (amountPaid < grandTotal) {
                handleError('Jumlah pembayaran kurang dari total belanja.');
                elements.amountPaid.classList.add('is-invalid');
                return;
            } else {
                elements.amountPaid.classList.remove('is-invalid');
            }
        } else if (state.payment.method === 'qris') {
            // For QRIS payment, ensure amount paid equals the grand total
            state.payment.amount_paid = grandTotal;
        } else {
            handleError('Metode pembayaran tidak valid.');
            return;
        }

        setLoading(true);

        // Calculate key values for submission
        const subtotal = parseInt(elements.subtotal.textContent.replace(/[^0-9]/g, ''), 10) || 0;
        const discountAmount = calculateDiscount(subtotal);
        const finalAmount = Math.max(0, subtotal - discountAmount);
        const changeAmount = state.payment.method === 'cash' ? Math.max(0, (parseInt(state.payment.amount_paid, 10) || 0) - finalAmount) : 0;
        
        const formData = {
            branch_id: state.branchId,
            customer_name: customerName,
            customer_phone: customerPhone,
            subtotal_amount: subtotal,
            discount_type: state.payment.discount_type,
            discount_value: discountValue,
            discount_amount: discountAmount,
            final_amount: finalAmount,
            payment_method: state.payment.method,
            paid_amount: parseInt(state.payment.amount_paid, 10) || 0,
            change_amount: changeAmount,
            items: state.cart.map(item => ({
                item_type: item.type,
                item_id: item.id,
                item_name: item.name,
                quantity: Math.round(item.quantity),
                unit_price: Math.round(item.price),
                subtotal: Math.round(item.quantity * item.price)
            })),
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        };

        try {
            const response = await fetch('/sales', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(formData),
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Terjadi kesalahan saat checkout.');
            }

            window.location.href = `/sales/${result.sale_id}`;
        } catch (error) {
            handleError(error.message);
        } finally {
            setLoading(false);
        }
    }

    function setLoading(isLoading) {
        state.loading = isLoading;
        elements.loadingOverlay.style.display = isLoading ? 'flex' : 'none';
    }
    
    function handleError(message) {
        state.error = message;
        elements.errorMessage.textContent = message;
        elements.errorMessage.style.display = 'block';
        setTimeout(() => {
            elements.errorMessage.style.display = 'none';
        }, 5000);
    }
    
    function toggleFormLocking() {
        // Check if the page contains sales form elements
        const salesForm = document.querySelector('#checkout-form');
        if (!salesForm) return;
        
        const cartContainer = document.querySelector('.checkout-panel');
        const productContainer = document.querySelector('.product-panel');
        
        // Remove existing lock overlays first
        const existingOverlays = document.querySelectorAll('.lock-overlay');
        existingOverlays.forEach(overlay => overlay.remove());
        
        if (state.isSpecialBranch) {
            console.log(`Locking sales form for ${state.branchName}`);
            
            // Lock the form and show warning
            const lockOverlay = document.createElement('div');
            lockOverlay.className = 'lock-overlay position-absolute w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3 top-0 start-0';
            lockOverlay.style.zIndex = '1000';
            lockOverlay.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
            lockOverlay.style.backdropFilter = 'blur(3px)';
            
            lockOverlay.innerHTML = `
                <div class="text-center p-4 rounded-3 shadow bg-white border border-warning mb-3">
                    <i class="bi bi-lock-fill text-warning fs-1 mb-3"></i>
                    <h4 class="fw-bold text-danger">Penjualan Tidak Diizinkan</h4>
                    <p class="text-muted mb-1">Penjualan tidak dapat dilakukan di <strong>${state.branchName}</strong>.</p>
                    <p class="small">Silahkan pilih cabang retail untuk melakukan transaksi penjualan.</p>
                </div>
            `;
            
            // Add lock to both product list and cart
            if (productContainer) {
                productContainer.style.position = 'relative';
                productContainer.style.overflow = 'hidden';
                const productLock = lockOverlay.cloneNode(true);
                productContainer.appendChild(productLock);
            }
            
            if (cartContainer) {
                cartContainer.style.position = 'relative';
                cartContainer.style.overflow = 'hidden';
                const cartLock = lockOverlay.cloneNode(true);
                cartContainer.appendChild(cartLock);
            }
            
            // Disable all form inputs
            const formInputs = salesForm.querySelectorAll('input, button, select, textarea');
            formInputs.forEach(input => {
                if (!input.closest('.branch-selector-container')) { // Don't disable branch selector
                    input.disabled = true;
                }
            });
            
            // Add a warning banner at the top
            const warningBanner = document.createElement('div');
            warningBanner.className = 'alert alert-warning d-flex align-items-center mb-3';
            warningBanner.innerHTML = `
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>
                    <strong>Perhatian:</strong> Anda berada di mode ${state.branchName}. Penjualan tidak dapat dilakukan di cabang ini.
                </div>
            `;
            
            const pageHeader = document.querySelector('.pos-container');
            if (pageHeader) {
                pageHeader.parentNode.insertBefore(warningBanner, pageHeader);
            }
            
        } else {
            // Remove warning banner if it exists
            const warningBanner = document.querySelector('.alert.alert-warning');
            if (warningBanner) warningBanner.remove();
            
            // Enable all form inputs except those that should remain disabled
            const formInputs = salesForm.querySelectorAll('input, button, select, textarea');
            formInputs.forEach(input => {
                if (!input.classList.contains('always-disabled')) {
                    input.disabled = false;
                }
            });
        }
    }
    
    function number_format(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    init();
});