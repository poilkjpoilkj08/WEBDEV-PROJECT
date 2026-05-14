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
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
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
    </style>
</head>
<body>
    @include('include.header')
    
    <div class="container-fluid main-content px-4 px-md-5 py-5" style="margin:0 auto; max-width: 1400px;">
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
    </script>
</body>
</html>