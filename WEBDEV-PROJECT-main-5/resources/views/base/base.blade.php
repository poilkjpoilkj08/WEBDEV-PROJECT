<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BookHive - {{ __('messages.books') }}</title>
    <!-- Bootswatch Cosmo theme - warm and modern for real estate -->
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/cosmo/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <!-- Axios for HTTP requests -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
    <script>
        // Configure axios globally for CSRF token and credentials
        // Wait a moment to ensure axios is fully loaded
        function configureAxios() {
            if (typeof axios === 'undefined') {
                console.warn('[BOOTSTRAP] Axios not loaded yet, retrying...');
                setTimeout(configureAxios, 50);
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            // Debug: Log CSRF token
            console.log('[BOOTSTRAP] CSRF Token found:', !!csrfToken, csrfToken ? csrfToken.substring(0, 20) + '...' : 'NONE');
            
            // Set CSRF token on all requests
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
            axios.defaults.headers.post['X-CSRF-TOKEN'] = csrfToken;
            
            // CRITICAL: Send cookies with requests (enables session authentication)
            axios.defaults.withCredentials = true;
            
            // Set response type for proper handling
            axios.defaults.responseType = 'json';
            
            // Verify configuration was applied
            console.log('[BOOTSTRAP] Axios Configured:', {
                'withCredentials': axios.defaults.withCredentials,
                'has_csrf_header': !!axios.defaults.headers.common['X-CSRF-TOKEN'],
                'csrf_preview': axios.defaults.headers.common['X-CSRF-TOKEN']?.substring(0, 20) + '...'
            });
            
            // Log axios requests in development
            axios.interceptors.request.use(config => {
                console.log('[AXIOS REQUEST]', {
                    method: config.method,
                    url: config.url,
                    has_csrf: !!config.headers['X-CSRF-TOKEN'],
                    withCredentials: config.withCredentials,
                    csrf_token: config.headers['X-CSRF-TOKEN']?.substring(0, 20) + '...',
                });
                return config;
            });
            
            axios.interceptors.response.use(
                response => {
                    console.log('[AXIOS RESPONSE] Success:', response.status, response.data);
                    return response;
                },
                error => {
                    console.error('[AXIOS RESPONSE] Error:', {
                        status: error.response?.status,
                        statusText: error.response?.statusText,
                        data: error.response?.data,
                        config: {
                            url: error.config?.url,
                            method: error.config?.method,
                            withCredentials: error.config?.withCredentials,
                        }
                    });
                    return Promise.reject(error);
                }
            );
        }
        
        // Try to configure immediately, fallback to after DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', configureAxios);
        } else {
            configureAxios();
        }
    </script>
    <style>
        * {
            box-sizing: border-box;
        }
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
        }
        nav.navbar {
            position: relative;
            width: 100%;
            height: auto;
            min-height: 70px;
        }
        .main-content {
            margin-top: 70px;
            flex: 1 0 auto;
            width: 100%;
            padding-top: 20px;
            padding-bottom: 40px;
        }
        @media (max-width: 768px) {
            .main-content {
                margin-top: 65px;
                padding-top: 20px;
            }
        }

        /* ===== GLOBAL MOBILE RESPONSIVENESS ===== */
        @media (max-width: 575.98px) {
            /* Containers have proper padding */
            .container, .container-fluid {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            /* Cards don't overflow */
            .card {
                border-radius: 10px !important;
            }
            /* Tables scroll horizontally */
            .table-responsive {
                -webkit-overflow-scrolling: touch;
            }
            /* Display-6 headings smaller on phone */
            .display-6 {
                font-size: 1.4rem !important;
            }
            /* Buttons full-width friendly */
            .btn-group-mobile-full .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            /* Order details sidebar stacks below on mobile — already col-lg-4/8 */
        }
        footer {
            flex-shrink: 0;
            width: 100%;
            min-height: 220px;
            padding-top: 3rem;
            padding-bottom: 3rem;
        }
        #logoutBtnDropdown {
            transition: all 0.2s ease;
        }
        #logoutBtnDropdown:hover {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }
        #logoutBtnDropdown:active {
            background-color: rgba(220, 53, 69, 0.2) !important;
        }

        /* Custom styles for the new theme */
        .hover-lift:hover {
            transform: translateY(-5px);
        }

        .bg-gradient {
            background: linear-gradient(135deg, #8B4513, #D2691E) !important;
        }

        .navbar-brand:hover {
            color: #ffd700 !important;
        }

        .card {
            border-radius: 15px !important;
            overflow: hidden;
        }

        .btn-warning {
            background: linear-gradient(135deg, #fd7e14, #ff8c00);
            border: none;
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #e8680d, #ff6b00);
            transform: translateY(-1px);
        }

        .badge {
            border-radius: 20px;
        }

        /* Language dropdown styling */
        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item.active {
            background-color: #e9ecef;
            font-weight: 600;
        }

        /* Property card enhancements */
        .card-img-top {
            transition: transform 0.3s ease;
        }

        .card:hover .card-img-top {
            transform: scale(1.05);
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .display-5 {
                font-size: 2.5rem;
            }

            .navbar-brand {
                font-size: 1.25rem !important;
            }

            /* Mobile navbar positioning */
            .navbar-toggler {
                position: fixed !important;
                right: 1rem !important;
                top: 1rem !important;
                z-index: 1001;
            }

            .navbar-brand {
                order: 1;
                margin-right: auto !important;
            }

            /* Ensure action hub appears on right but doesn't overlap hamburger */
            .navbar .d-flex {
                position: fixed !important;
                right: 0.5rem !important;
                top: 1rem !important;
                z-index: 1000;
            }

            /* Collapse menu should stack below on mobile */
            .navbar-collapse {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: inherit;
                width: 100%;
                border-radius: 0 0 8px 8px;
            }
        }

        /* Toast Notification Styles */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 16px 20px;
            z-index: 9999;
            display: none;
            animation: slideInRight 0.3s ease-out;
        }

        .toast-notification.show {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .toast-notification.success {
            border-left: 4px solid #28a745;
        }

        .toast-notification.success .toast-icon {
            color: #28a745;
        }

        .toast-notification.error {
            border-left: 4px solid #dc3545;
        }

        .toast-notification.error .toast-icon {
            color: #dc3545;
        }

        .toast-notification.info {
            border-left: 4px solid #17a2b8;
        }

        .toast-notification.info .toast-icon {
            color: #17a2b8;
        }

        .toast-icon {
            font-size: 20px;
            flex-shrink: 0;
        }

        .toast-content {
            flex: 1;
        }

        .toast-message {
            font-weight: 500;
            color: #333;
            margin: 0;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .toast-notification.hide {
            animation: slideOutRight 0.3s ease-out;
        }

        @media (max-width: 768px) {
            .toast-notification {
                min-width: 280px;
                right: 10px;
                left: 10px;
            }
        }

        /* ===== RESPONSIVE MODALS ===== */
        @media (max-width: 576px) {
            /* Make modals full-height on mobile */
            .modal-dialog {
                margin: 0.5rem;
                min-height: auto;
            }

            /* Adjust modal sizes for mobile */
            .modal-dialog.modal-lg {
                max-width: 95% !important;
            }

            .modal-dialog.modal-sm {
                max-width: 90% !important;
            }

            /* Default modal takes up most of screen */
            .modal-dialog {
                max-width: 95% !important;
            }

            /* Make modal body scrollable and not overflow */
            .modal-body {
                max-height: calc(100vh - 200px);
                overflow-y: auto;
            }

            /* Improve button sizing in modals on mobile */
            .modal-footer .btn {
                padding: 0.5rem 1rem;
                font-size: 0.95rem;
            }

            /* Make form labels and inputs full width */
            .modal .form-label {
                font-size: 0.95rem;
            }

            .modal .form-control,
            .modal .form-select {
                font-size: 1rem;
                padding: 0.65rem 0.75rem;
            }

            /* Textarea in modals */
            .modal textarea.form-control {
                min-height: 100px;
                resize: vertical;
            }
        }

        /* Tablet responsiveness */
        @media (max-width: 768px) and (min-width: 577px) {
            .modal-dialog.modal-lg {
                max-width: 80%;
            }

            .modal-dialog {
                max-width: 85%;
            }
        }

        /* Image preview in modals */
        .modal .refund-thumb {
            cursor: zoom-in;
            transition: transform 0.3s ease;
        }

        .modal .refund-thumb:hover {
            transform: scale(1.05);
        }

        /* Evidence images in modals should be responsive */
        .modal-body img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* File upload drag-drop area responsive */
        @media (max-width: 576px) {
            #refundUploadArea {
                padding: 1.5rem !important;
            }

            #refundUploadArea .fa-2x {
                font-size: 1.5rem !important;
            }

            #refundUploadArea p {
                font-size: 0.9rem;
            }
        }

        /* ===== ADMIN PAGES RESPONSIVE STYLES ===== */
        @media (max-width: 768px) {
            /* Button groups wrap on mobile */
            .btn-group {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .btn-group .btn {
                flex: 1 1 auto;
                min-width: 100px;
            }

            /* Stat cards better on tablet */
            .row.g-3 .col-md-3 {
                margin-bottom: 0.5rem;
            }

            /* Admin table more readable */
            .table {
                font-size: 0.9rem;
            }

            .table-responsive {
                -webkit-overflow-scrolling: touch;
            }

            /* Admin page heading */
            .h3 {
                font-size: 1.5rem;
            }

            /* Badge sizing in tables */
            .badge {
                padding: 0.4rem 0.6rem;
                font-size: 0.8rem;
            }

            /* Action buttons more accessible */
            .d-flex.gap-2 .btn-sm {
                padding: 0.4rem 0.6rem;
            }
        }

        @media (max-width: 576px) {
            /* Extra small screens - admin optimization */
            .btn-group {
                width: 100%;
            }

            .btn-group .btn {
                flex: 1 1 100%;
                margin-bottom: 0.25rem;
            }

            /* Stats display better */
            .row.g-3 > [class*='col-'] {
                margin-bottom: 0.5rem;
            }

            /* Table font sizing */
            .table th {
                font-size: 0.75rem;
                padding: 0.5rem;
            }

            .table td {
                font-size: 0.85rem;
                padding: 0.5rem;
            }

            /* Compact action buttons */
            .btn-sm {
                padding: 0.35rem 0.6rem;
                font-size: 0.8rem;
            }

            /* Icon sizing in tables */
            .fa {
                font-size: 0.9rem;
            }

            /* Hide non-essential info on small screens */
            .d-none-sm {
                display: none !important;
            }

            /* Text truncation for emails */
            .text-truncate {
                max-width: 150px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            /* Better modal positioning on admin pages */
            .modal-dialog {
                margin: 0.5rem auto;
            }
        }

        /* Tablet responsiveness for admin */
        @media (max-width: 768px) and (min-width: 577px) {
            .btn-group .btn {
                padding: 0.5rem 0.75rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- Toast Notification --}}
    <div id="toastNotification" class="toast-notification">
        <div class="d-flex align-items-start w-100">
            <i class="fas fa-check-circle toast-icon me-2"></i>
            <div class="toast-content flex-grow-1">
                <p class="toast-message mb-0" id="toastMessage">Added to cart!</p>
            </div>
            <button type="button" class="btn-close btn-sm" id="toastCloseBtn" style="margin-left: auto; margin-top: -2px;"></button>
        </div>
    </div>

    @include('include.header')
    
    <div class="container-fluid main-content px-4 px-md-5 py-5" style="margin-left: auto; margin-right: auto; max-width: 1400px;">
        @yield('content')
    </div>
    
    @include('include.footer')

    <script>
        // Language switching functionality
        function changeLanguage(lang, showNotification = false) {
            // Store language preference in localStorage
            localStorage.setItem('preferred_language', lang);

            // Update UI to show selected language
            const dropdown = document.getElementById('languageDropdown');
            if (dropdown) {
                const langText = dropdown.querySelector('.d-none.d-sm-inline');
                if (langText) {
                    langText.textContent = lang.toUpperCase();
                }

                // Update dropdown items
                const items = document.querySelectorAll('#languageDropdown + .dropdown-menu .dropdown-item');
                items.forEach(item => {
                    const checkIcon = item.querySelector('.fa-check');
                    const dotIcon = item.querySelector('.text-muted');
                    if (checkIcon) checkIcon.remove();
                    if (dotIcon) dotIcon.innerHTML = '○';
                });

                // Mark selected language
                const selectedItem = [...items].find(item => item.onclick && item.onclick.toString().includes(`'${lang}'`));
                if (selectedItem) {
                    const dotSpan = selectedItem.querySelector('.text-muted');
                    if (dotSpan) {
                        dotSpan.innerHTML = '<i class="fas fa-check text-success"></i>';
                    }
                }
            }

            // Here you would typically reload the page with the new language
            // or make an AJAX call to change the language server-side
            console.log('Language changed to:', lang);

            // Only show notification if this is a manual change (not page load)
            if (showNotification) {
                showLanguageNotification(lang);
            }
        }

        function showLanguageNotification(lang) {
            const languages = {
                'en': 'English',
                'id': 'Indonesian',
                'es': 'Español',
                'fr': 'Français',
                'de': 'Deutsch',
                'it': 'Italiano'
            };

            // Create notification element
            const notification = document.createElement('div');
            notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                Language changed to ${languages[lang] || lang}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            // Auto remove after 3 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 3000);
        }

        // Initialize language on page load (without showing notification)
        document.addEventListener('DOMContentLoaded', function() {
            const savedLang = localStorage.getItem('preferred_language') || 'en';
            changeLanguage(savedLang, false);
        });

        // Enhanced dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Custom user dropdown toggle
            const userDropdownBtn = document.getElementById('customUserDropdown');
            const userMenu = document.getElementById('customUserMenu');

            if (userDropdownBtn && userMenu) {
                userDropdownBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isVisible = userMenu.style.display === 'block';
                    userMenu.style.display = isVisible ? 'none' : 'block';
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userDropdownBtn.contains(e.target) && !userMenu.contains(e.target)) {
                        userMenu.style.display = 'none';
                    }
                });
            }
        });

        // Toast Notification Function
        let toastTimeout;
        window.showToast = function(message = 'Added to cart!', type = 'success', duration = 2500) {
            const toast = document.getElementById('toastNotification');
            const toastMessage = document.getElementById('toastMessage');
            const toastIcon = toast.querySelector('.toast-icon');
            const closeBtn = document.getElementById('toastCloseBtn');
            
            // Clear any existing timeout
            if (toastTimeout) clearTimeout(toastTimeout);
            
            // Remove previous classes
            toast.classList.remove('success', 'error', 'info', 'hide');
            
            // Set new content and type
            toastMessage.textContent = message;
            toast.classList.add(type, 'show');
            
            // Update icon based on type
            if (type === 'success') {
                toastIcon.className = 'fas fa-check-circle toast-icon me-2';
            } else if (type === 'error') {
                toastIcon.className = 'fas fa-exclamation-circle toast-icon me-2';
            } else if (type === 'info' || type === 'warning') {
                toastIcon.className = 'fas fa-info-circle toast-icon me-2';
            }
            
            // Close button handler
            const closeToast = () => {
                toast.classList.add('hide');
                if (toastTimeout) clearTimeout(toastTimeout);
                setTimeout(() => {
                    toast.classList.remove('show', 'hide');
                }, 300);
            };
            
            closeBtn.onclick = closeToast;
            
            // Hide after duration
            toastTimeout = setTimeout(closeToast, duration);
        };

        // Clear toast on page navigation
        window.addEventListener('beforeunload', function() {
            if (toastTimeout) clearTimeout(toastTimeout);
        });
    </script>
    @stack('scripts')
</body>
</html>