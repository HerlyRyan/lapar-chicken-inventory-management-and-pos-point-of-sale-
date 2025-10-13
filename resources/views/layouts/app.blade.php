<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lapar Chicken InventPOS</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/Logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/Logo.png') }}">

    <!-- External Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- jQuery for legacy scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 font-sans antialiased">
    <!-- Header -->
    @include('layouts.partial.header')

    <!-- Mobile Backdrop -->
    <div id="mobile-backdrop"
        class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden transition-opacity duration-300 opacity-0 pointer-events-none"
        onclick="closeMobileSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed left-0 top-0 h-full w-64 bg-white shadow-xl border-r border-gray-200 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out pt-16">
        <div class="p-4 h-full overflow-y-auto">
            @include('layouts.partial.sidebar')
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="lg:ml-64 min-h-screen">
        <!-- Main Content -->
        <main class="pt-16 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto py-6">
                @yield('content')
            </div>
        </main>



        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-4 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto text-center">
                <p class="text-sm text-gray-600">
                    &copy; {{ date('Y') }} Lapar Chicken. Sistem Inventori & Penjualan. All rights reserved.
                </p>
            </div>
        </footer>
    </div>

    <!-- External Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Mobile Sidebar Functions
        function openMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('mobile-backdrop');

            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-100');
            document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('mobile-backdrop');

            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            backdrop.classList.remove('opacity-100');
            document.body.classList.remove('overflow-hidden');
        }

        // Initialize sidebar state
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggleBtn = document.getElementById('sidebarToggle');
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            if (isCollapsed) {
                const sidebar = document.getElementById('sidebar');
                const mainWrapper = sidebar.nextElementSibling;
                sidebar.classList.add('lg:-translate-x-full');
                mainWrapper.classList.remove('lg:ml-64');
            }

            // Add click handler for toggle button
            if (sidebarToggleBtn) {
                sidebarToggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Check if mobile or desktop
                    if (window.innerWidth < 1024) {
                        openMobileSidebar();
                    } else {
                        toggleDesktopSidebar();
                    }
                });
            }

            // Close mobile sidebar when window resizes to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    closeMobileSidebar();
                }
            });
        });

        // Branch selection and propagation
        document.addEventListener('DOMContentLoaded', function() {
            if (window.__BRANCH_INIT_DONE) return;
            window.__BRANCH_INIT_DONE = true;

            const branchDropdownItems = document.querySelectorAll('.branch-selector .dropdown-item');
            branchDropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    const href = this.getAttribute('href') || '';
                    const url = new URL(href, window.location.origin);
                    const branchId = url.searchParams.get('branch_id');
                    const branchName = this.querySelector('span') ? this.querySelector('span')
                        .textContent.trim() : 'Semua Cabang';

                    if (branchId) {
                        sessionStorage.setItem('selectedBranchId', branchId);
                        sessionStorage.setItem('selectedBranchName', branchName);
                    } else {
                        sessionStorage.removeItem('selectedBranchId');
                        sessionStorage.removeItem('selectedBranchName');
                    }

                    if (typeof window.updateProductStocksForBranch === 'function') {
                        e.preventDefault();
                        const newPath = url.pathname + (branchId ? ('?branch_id=' + branchId) : '');
                        window.dispatchEvent(new CustomEvent('branchChanged', {
                            detail: {
                                branchId,
                                branchName,
                                url: newPath
                            }
                        }));
                        return;
                    }

                    e.preventDefault();
                    const currentPath = window.location.pathname;
                    const newUrl = currentPath + (branchId ? '?branch_id=' + branchId : '');
                    window.location.href = newUrl;
                });
            });

            function addBranchIdToLinksAndForms() {
                const urlParams = new URLSearchParams(window.location.search);
                let branchId = urlParams.get('branch_id') || sessionStorage.getItem('selectedBranchId');

                if (!branchId) return;
                sessionStorage.setItem('selectedBranchId', branchId);

                // Update links
                const selector =
                    'a:not([href^="http"]):not([href^="mailto"]):not([href^="tel"]):not([href^="#"]):not([href="javascript:void(0)"])';
                const links = document.querySelectorAll(selector);
                links.forEach(link => {
                    if (link.classList.contains('dropdown-item') && link.closest('.branch-selector'))
                        return;
                    if (!link.href || link.href.trim() === '') return;
                    try {
                        const u = new URL(link.href, window.location.origin);
                        if (u.searchParams.get('branch_id') === branchId) return;
                        u.searchParams.set('branch_id', branchId);
                        link.href = u.toString();
                    } catch (_) {}
                });

                // Update forms
                const forms = document.querySelectorAll('form:not([action^="http"])');
                forms.forEach(form => {
                    let input = form.querySelector('input[name="branch_id"]');
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'branch_id';
                        form.appendChild(input);
                    }
                    input.value = branchId;

                    if (!form.__branchSubmitHooked) {
                        form.addEventListener('submit', function() {
                            const currentBranchId = new URLSearchParams(window.location.search).get(
                                'branch_id') || sessionStorage.getItem('selectedBranchId');
                            if (!currentBranchId) return;
                            let brInput = form.querySelector('input[name="branch_id"]');
                            if (!brInput) {
                                brInput = document.createElement('input');
                                brInput.type = 'hidden';
                                brInput.name = 'branch_id';
                                form.appendChild(brInput);
                            }
                            brInput.value = currentBranchId;
                        });
                        form.__branchSubmitHooked = true;
                    }
                });
            }

            addBranchIdToLinksAndForms();

            if (!window.__BRANCH_LINKS_OBSERVER) {
                window.__BRANCH_LINKS_OBSERVER = new MutationObserver(function(mutations) {
                    let shouldRun = false;
                    for (const mutation of mutations) {
                        if (mutation.type === 'childList' && mutation.addedNodes && mutation.addedNodes
                            .length) {
                            shouldRun = true;
                            break;
                        }
                    }
                    if (!shouldRun) return;

                    if (window.__BRANCH_OBSERVER_SCHEDULED) return;
                    window.__BRANCH_OBSERVER_SCHEDULED = true;
                    setTimeout(() => {
                        window.__BRANCH_OBSERVER_SCHEDULED = false;
                        addBranchIdToLinksAndForms();
                    }, 100);
                });
                window.__BRANCH_LINKS_OBSERVER.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }
        });

        // Indonesian form validation
        document.addEventListener('DOMContentLoaded', function() {
            const requiredFields = document.querySelectorAll(
                'input[required], select[required], textarea[required]');

            requiredFields.forEach(function(field) {
                field.addEventListener('invalid', function(e) {
                    const label = document.querySelector('label[for="' + field.id + '"]');
                    let fieldName = 'Field ini';

                    if (label) {
                        fieldName = label.textContent.replace(/\s*\*\s*$/, '').trim();
                    } else if (field.placeholder) {
                        fieldName = field.placeholder;
                    }

                    if (field.validity.valueMissing) {
                        field.setCustomValidity(fieldName + ' wajib diisi.');
                    } else if (field.validity.typeMismatch) {
                        if (field.type === 'email') {
                            field.setCustomValidity(fieldName + ' harus berupa email yang valid.');
                        } else if (field.type === 'number') {
                            field.setCustomValidity(fieldName + ' harus berupa angka.');
                        } else {
                            field.setCustomValidity(fieldName + ' format tidak valid.');
                        }
                    } else if (field.validity.rangeUnderflow) {
                        field.setCustomValidity(fieldName + ' harus minimal ' + field.min + '.');
                    } else if (field.validity.rangeOverflow) {
                        field.setCustomValidity(fieldName + ' harus maksimal ' + field.max + '.');
                    }
                });

                field.addEventListener('input', function() {
                    field.setCustomValidity('');
                });
            });
        });
    </script>

    <!-- Global Delete Confirmation -->
    <script>
        function confirmDelete(url, name) {
            if (url.startsWith('http')) {
                try {
                    const urlObj = new URL(url);
                    url = urlObj.pathname;
                } catch (e) {
                    console.error('Invalid URL format:', url);
                }
            }

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Yakin ingin menghapus ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

    @stack('scripts')
</body>

</html>
