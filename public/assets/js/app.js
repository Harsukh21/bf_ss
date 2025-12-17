/**
 * Laravel Application Common JavaScript
 */

// Global App Object
window.App = {
    // Configuration
    config: {
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        baseUrl: window.location.origin,
        apiUrl: window.location.origin + '/api',
    },

    // Utility Functions
    utils: {
        // Debounce function
        debounce: function(func, wait, immediate) {
            let timeout;
            return function executedFunction() {
                const context = this;
                const args = arguments;
                const later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        },

        // Throttle function
        throttle: function(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        // Format currency
        formatCurrency: function(amount, currency = 'USD') {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: currency
            }).format(amount);
        },

        // Format date
        formatDate: function(date, options = {}) {
            const defaultOptions = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return new Date(date).toLocaleDateString('en-US', { ...defaultOptions, ...options });
        },

        // Copy to clipboard
        copyToClipboard: function(text) {
            if (navigator.clipboard) {
                return navigator.clipboard.writeText(text);
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                return Promise.resolve();
            }
        },

        // Show notification
        showNotification: function(message, type = 'info', duration = 5000) {
            const notification = this.createNotification(message, type);
            document.body.appendChild(notification);

            // Auto remove after duration
            if (duration > 0) {
                setTimeout(() => {
                    this.removeNotification(notification);
                }, duration);
            }

            return notification;
        },

        // Create notification element
        createNotification: function(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            
            const icon = this.getNotificationIcon(type);
            
            notification.innerHTML = `
                <div class="flex items-center p-4">
                    <div class="flex-shrink-0">
                        ${icon}
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">${message}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <button type="button" class="inline-flex text-gray-400 hover:text-gray-600 notification-close">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;

            // Add close functionality
            notification.querySelector('.notification-close').addEventListener('click', () => {
                this.removeNotification(notification);
            });

            return notification;
        },

        // Get notification icon
        getNotificationIcon: function(type) {
            const icons = {
                success: '<svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
                error: '<svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>',
                warning: '<svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
                info: '<svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
            };
            return icons[type] || icons.info;
        },

        // Remove notification
        removeNotification: function(notification) {
            notification.style.transition = 'all 0.3s ease-in-out';
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }
    },

    // Form Handling
    forms: {
        // Initialize form validation
        init: function() {
            this.bindValidation();
            this.bindSubmit();
        },

        // Bind validation events
        bindValidation: function() {
            const forms = document.querySelectorAll('form[data-validate]');
            forms.forEach(form => {
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.addEventListener('blur', () => this.validateField(input));
                    input.addEventListener('input', this.utils.debounce(() => this.validateField(input), 300));
                });
            });
        },

        // Validate individual field
        validateField: function(field) {
            const value = field.value.trim();
            const type = field.type;
            const required = field.hasAttribute('required');
            let isValid = true;
            let message = '';

            // Required validation
            if (required && !value) {
                isValid = false;
                message = 'This field is required.';
            }

            // Email validation
            if (type === 'email' && value && !this.isValidEmail(value)) {
                isValid = false;
                message = 'Please enter a valid email address.';
            }

            // Password validation
            if (type === 'password' && value && value.length < 6) {
                isValid = false;
                message = 'Password must be at least 6 characters.';
            }

            this.showFieldError(field, isValid ? '' : message);
            return isValid;
        },

        // Show field error
        showFieldError: function(field, message) {
            const errorElement = field.parentNode.querySelector('.field-error');
            
            if (message) {
                field.classList.add('form-input-error');
                if (!errorElement) {
                    const error = document.createElement('p');
                    error.className = 'field-error form-error';
                    error.textContent = message;
                    field.parentNode.appendChild(error);
                } else {
                    errorElement.textContent = message;
                }
            } else {
                field.classList.remove('form-input-error');
                if (errorElement) {
                    errorElement.remove();
                }
            }
        },

        // Check if email is valid
        isValidEmail: function(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },

        // Bind form submit
        bindSubmit: function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', (e) => {
                    if (form.hasAttribute('data-validate')) {
                        if (!this.validateForm(form)) {
                            e.preventDefault();
                        }
                    }
                });
            });
        },

        // Validate entire form
        validateForm: function(form) {
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!this.validateField(input)) {
                    isValid = false;
                }
            });

            return isValid;
        }
    },

    // Alert Handling
    alerts: {
        // Initialize alert functionality
        init: function() {
            this.bindCloseButtons();
        },

        // Bind close buttons
        bindCloseButtons: function() {
            document.addEventListener('click', (e) => {
                if (e.target.closest('.alert-close-btn')) {
                    const alert = e.target.closest('.alert');
                    if (alert) {
                        this.closeAlert(alert);
                    }
                }
            });
        },

        // Close alert with animation
        closeAlert: function(alert) {
            alert.style.transition = 'all 0.3s ease-in-out';
            alert.style.opacity = '0';
            alert.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 300);
        }
    },

    // Smooth Scrolling
    smoothScroll: {
        // Initialize smooth scrolling
        init: function() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', (e) => {
                    const href = anchor.getAttribute('href');
                    // Skip if href is just '#' or empty
                    if (!href || href === '#' || href.length <= 1) {
                        return; // Let default behavior or other handlers work
                    }
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        }
    },

    // Mobile Menu
    mobileMenu: {
        // Initialize mobile menu
        init: function() {
            const toggle = document.querySelector('.mobile-menu-toggle');
            const menu = document.querySelector('.mobile-menu');

            if (toggle && menu) {
                toggle.addEventListener('click', () => {
                    menu.classList.toggle('active');
                    toggle.classList.toggle('active');
                });

                // Close menu when clicking outside
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.mobile-menu') && !e.target.closest('.mobile-menu-toggle')) {
                        menu.classList.remove('active');
                        toggle.classList.remove('active');
                    }
                });
            }
        }
    },

    // Loading States
    loading: {
        // Show loading spinner
        show: function(element) {
            if (typeof element === 'string') {
                element = document.querySelector(element);
            }
            
            if (element) {
                element.innerHTML = '<div class="spinner"></div>';
                element.classList.add('loading');
            }
        },

        // Hide loading spinner
        hide: function(element) {
            if (typeof element === 'string') {
                element = document.querySelector(element);
            }
            
            if (element) {
                element.classList.remove('loading');
            }
        }
    },

    // AJAX Helper
    ajax: {
        // Make AJAX request
        request: function(url, options = {}) {
            const defaultOptions = {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': App.config.csrfToken
                }
            };

            const config = { ...defaultOptions, ...options };

            return fetch(url, config)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('AJAX Error:', error);
                    App.utils.showNotification('An error occurred. Please try again.', 'error');
                    throw error;
                });
        },

        // GET request
        get: function(url, options = {}) {
            return this.request(url, { ...options, method: 'GET' });
        },

        // POST request
        post: function(url, data, options = {}) {
            return this.request(url, {
                ...options,
                method: 'POST',
                body: JSON.stringify(data)
            });
        },

        // PUT request
        put: function(url, data, options = {}) {
            return this.request(url, {
                ...options,
                method: 'PUT',
                body: JSON.stringify(data)
            });
        },

        // DELETE request
        delete: function(url, options = {}) {
            return this.request(url, { ...options, method: 'DELETE' });
        }
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all modules
    App.forms.init();
    App.alerts.init();
    App.smoothScroll.init();
    App.mobileMenu.init();
    
    // Initialize new modules
    if (window.themeToggle) themeToggle.init();
    if (window.userDropdown) userDropdown.init();

    // Add loading states to buttons
    document.querySelectorAll('button[type="submit"]').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            if (form) {
                App.loading.show(this);
            }
        });
    });

    // Add copy to clipboard functionality
    document.querySelectorAll('[data-copy]').forEach(element => {
        element.addEventListener('click', function() {
            const text = this.getAttribute('data-copy');
            App.utils.copyToClipboard(text).then(() => {
                App.utils.showNotification('Copied to clipboard!', 'success', 2000);
            });
        });
    });

});

// Dark Mode Toggle
window.themeToggle = {
    init: function() {
        this.loadTheme();
        this.bindToggle();
    },

    loadTheme: function() {
        const savedTheme = localStorage.getItem('theme');
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        const theme = savedTheme || systemTheme;
        
        this.setTheme(theme);
    },

    setTheme: function(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    },

    bindToggle: function() {
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const isDark = document.documentElement.classList.contains('dark');
                this.setTheme(isDark ? 'light' : 'dark');
            });
        }
    }
};

// User Dropdown Toggle
window.userDropdown = {
    init: function() {
        this.bindToggle();
        this.bindOutsideClick();
    },

    bindToggle: function() {
        const toggle = document.getElementById('userDropdownToggle');
        const dropdown = document.getElementById('userDropdown');
        const arrow = document.getElementById('userDropdownArrow');

        if (toggle && dropdown) {
            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = dropdown.classList.contains('hidden');
                
                // Close all other dropdowns first
                this.closeAllDropdowns();
                
                if (isHidden) {
                    dropdown.classList.remove('hidden');
                    if (arrow) arrow.style.transform = 'rotate(180deg)';
                } else {
                    dropdown.classList.add('hidden');
                    if (arrow) arrow.style.transform = 'rotate(0deg)';
                }
            });
        }
    },

    bindOutsideClick: function() {
        document.addEventListener('click', (e) => {
            const dropdown = document.getElementById('userDropdown');
            const toggle = document.getElementById('userDropdownToggle');
            
            if (dropdown && toggle && !toggle.contains(e.target) && !dropdown.contains(e.target)) {
                this.closeAllDropdowns();
            }
        });
    },

    closeAllDropdowns: function() {
        const dropdown = document.getElementById('userDropdown');
        const arrow = document.getElementById('userDropdownArrow');
        
        if (dropdown) dropdown.classList.add('hidden');
        if (arrow) arrow.style.transform = 'rotate(0deg)';
    }
};

// Dropdown functionality
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId + '-dropdown');
    const arrow = document.getElementById(dropdownId + '-arrow');
    
    if (dropdown && arrow) {
        const isHidden = dropdown.classList.contains('hidden');
        
        if (isHidden) {
            dropdown.classList.remove('hidden');
            arrow.style.transform = 'rotate(180deg)';
        } else {
            dropdown.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
        }
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = App;
}
