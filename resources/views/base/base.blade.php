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
    </style>
</head>
<body>
    {{-- Toast Notification --}}
    <div id="toastNotification" class="toast-notification">
        <i class="fas fa-check-circle toast-icon"></i>
        <div class="toast-content">
            <p class="toast-message" id="toastMessage">Added to cart!</p>
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
        window.showToast = function(message = 'Added to cart!', type = 'success', duration = 5000) {
            const toast = document.getElementById('toastNotification');
            const toastMessage = document.getElementById('toastMessage');
            const toastIcon = toast.querySelector('.toast-icon');
            
            // Clear any existing timeout
            if (toastTimeout) clearTimeout(toastTimeout);
            
            // Remove previous classes
            toast.classList.remove('success', 'error', 'info', 'hide');
            
            // Set new content and type
            toastMessage.textContent = message;
            toast.classList.add(type, 'show');
            
            // Update icon based on type
            if (type === 'success') {
                toastIcon.className = 'fas fa-check-circle toast-icon';
            } else if (type === 'error') {
                toastIcon.className = 'fas fa-exclamation-circle toast-icon';
            } else if (type === 'info') {
                toastIcon.className = 'fas fa-info-circle toast-icon';
            }
            
            // Hide after duration
            toastTimeout = setTimeout(() => {
                toast.classList.add('hide');
                setTimeout(() => {
                    toast.classList.remove('show', 'hide');
                }, 300);
            }, duration);
        };

        // Clear toast on page navigation
        window.addEventListener('beforeunload', function() {
            if (toastTimeout) clearTimeout(toastTimeout);
        });
    </script>
</body>
</html>