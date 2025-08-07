// assets/js/main.js - JavaScript หลัก

// Main Application Object
const InventionVote = {
    init: function() {
        this.setupEventListeners();
        this.initializePage();
        this.setupFormValidation();
        this.setupModals();
        this.setupTooltips();
        this.setupConfirmDialogs();
        this.loadingManager.init();
        console.log('INVENTION-VOTE System Initialized');
    },
    
    // Event Listeners
    setupEventListeners: function() {
        // Mobile menu toggle
        document.addEventListener('click', (e) => {
            if (e.target.matches('.mobile-menu-toggle') || e.target.matches('.mobile-menu-toggle *')) {
                this.toggleSidebar();
            }
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            const sidebar = document.querySelector('.sidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth <= 992 && sidebar && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
        
        // Form submissions
        document.addEventListener('submit', (e) => {
            if (e.target.matches('form[data-ajax="true"]')) {
                e.preventDefault();
                this.handleAjaxForm(e.target);
            }
        });
        
        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', () => {
            this.setupAlerts();
        });
        
        // Window resize handler
        window.addEventListener('resize', () => {
            this.handleResize();
        });
    },
    
    // Initialize page-specific functionality
    initializePage: function() {
        // Setup sidebar active states
        this.setupSidebarActive();
        
        // Setup data tables if present
        this.setupDataTables();
        
        // Setup date pickers
        this.setupDatePickers();
        
        // Setup file uploads
        this.setupFileUploads();
        
        // Setup search functionality
        this.setupSearch();
    },
    
    // Sidebar Management
    toggleSidebar: function() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.toggle('show');
        }
    },
    
    setupSidebarActive: function() {
        const currentPath = window.location.pathname;
        const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
        
        sidebarLinks.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
                
                // Open parent submenu if exists
                const parentSubmenu = link.closest('.submenu');
                if (parentSubmenu) {
                    parentSubmenu.classList.add('show');
                }
            }
        });
    },
    
    // Handle window resize
    handleResize: function() {
        const sidebar = document.querySelector('.sidebar');
        
        if (window.innerWidth > 992 && sidebar) {
            sidebar.classList.remove('show');
        }
    },
    
    // Alert Management
    setupAlerts: function() {
        const alerts = document.querySelectorAll('.alert[data-auto-hide]');
        
        alerts.forEach(alert => {
            const delay = parseInt(alert.dataset.autoHide) || 5000;
            
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 500);
            }, delay);
        });
        
        // Close button functionality
        document.addEventListener('click', (e) => {
            if (e.target.matches('.alert .btn-close')) {
                const alert = e.target.closest('.alert');
                if (alert) {
                    alert.style.transition = 'opacity 0.3s ease';
                    alert.style.opacity = '0';
                    
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 300);
                }
            }
        });
    },
    
    // Show alert message
    showAlert: function(type, message, autoHide = true) {
        const alertContainer = document.querySelector('.alert-container') || document.body;
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} ${autoHide ? 'alert-dismissible' : ''}`;
        alert.innerHTML = `
            ${message}
            ${autoHide ? '<button type="button" class="btn-close" aria-label="Close">×</button>' : ''}
        `;
        
        alertContainer.appendChild(alert);
        
        if (autoHide) {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 500);
            }, 5000);
        }
    },
    
    // Form Validation
    setupFormValidation: function() {
        const forms = document.querySelectorAll('form[data-validate="true"]');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });
            
            // Real-time validation
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateInput(input);
                });
            });
        });
    },
    
    validateForm: function(form) {
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!this.validateInput(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    },
    
    validateInput: function(input) {
        const value = input.value.trim();
        let isValid = true;
        let message = '';
        
        // Clear previous errors
        this.clearInputError(input);
        
        // Required validation
        if (input.hasAttribute('required') && !value) {
            isValid = false;
            message = 'กรุณากรอกข้อมูลในช่องนี้';
        }
        
        // Email validation
        else if (input.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                message = 'กรุณากรอกอีเมลที่ถูกต้อง';
            }
        }
        
        // Password validation
        else if (input.type === 'password' && value && input.hasAttribute('data-min-length')) {
            const minLength = parseInt(input.dataset.minLength);
            if (value.length < minLength) {
                isValid = false;
                message = `รหัสผ่านต้องมีความยาวอย่างน้อย ${minLength} ตัวอักษร`;
            }
        }
        
        // Confirm password validation
        else if (input.hasAttribute('data-confirm')) {
            const originalPassword = document.querySelector(input.dataset.confirm);
            if (originalPassword && value !== originalPassword.value) {
                isValid = false;
                message = 'รหัสผ่านไม่ตรงกัน';
            }
        }
        
        if (!isValid) {
            this.showInputError(input, message);
        }
        
        return isValid;
    },
    
    showInputError: function(input, message) {
        input.classList.add('is-invalid');
        
        let errorDiv = input.parentNode.querySelector('.invalid-feedback');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            input.parentNode.appendChild(errorDiv);
        }
        
        errorDiv.textContent = message;
    },
    
    clearInputError: function(input) {
        input.classList.remove('is-invalid');
        
        const errorDiv = input.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    },
    
    // AJAX Form Handling
    handleAjaxForm: function(form) {
        const formData = new FormData(form);
        const url = form.action;
        const method = form.method || 'POST';
        
        this.loadingManager.show();
        
        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            this.loadingManager.hide();
            
            if (data.success) {
                this.showAlert('success', data.message);
                
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                }
                
                if (data.reload) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
                
                if (data.reset_form) {
                    form.reset();
                }
            } else {
                this.showAlert('danger', data.message);
                
                if (data.errors) {
                    this.showFormErrors(form, data.errors);
                }
            }
        })
        .catch(error => {
            this.loadingManager.hide();
            this.showAlert('danger', 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง');
            console.error('Ajax form error:', error);
        });
    },
    
    showFormErrors: function(form, errors) {
        Object.keys(errors).forEach(fieldName => {
            const input = form.querySelector(`[name="${fieldName}"]`);
            if (input) {
                this.showInputError(input, errors[fieldName]);
            }
        });
    },
    
    // Modal Management
    setupModals: function() {
        document.addEventListener('click', (e) => {
            // Open modal
            if (e.target.matches('[data-toggle="modal"]')) {
                e.preventDefault();
                const target = e.target.getAttribute('data-target');
                this.openModal(target);
            }
            
            // Close modal
            if (e.target.matches('.modal .btn-close') || 
                e.target.matches('.modal[data-dismiss="modal"]') ||
                (e.target.matches('.modal') && !e.target.closest('.modal-content'))) {
                this.closeModal(e.target.closest('.modal'));
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal.show');
                if (openModal) {
                    this.closeModal(openModal);
                }
            }
        });
    },
    
    openModal: function(selector) {
        const modal = document.querySelector(selector);
        if (modal) {
            modal.classList.add('show');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Focus first input
            const firstInput = modal.querySelector('input, select, textarea, button');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 100);
            }
        }
    },
    
    closeModal: function(modal) {
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    },
    
    // Confirmation Dialogs
    setupConfirmDialogs: function() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-confirm]') || e.target.closest('[data-confirm]')) {
                const element = e.target.matches('[data-confirm]') ? e.target : e.target.closest('[data-confirm]');
                const message = element.getAttribute('data-confirm');
                
                if (!confirm(message)) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    },
    
    // Loading Manager
    loadingManager: {
        init: function() {
            this.createLoadingOverlay();
        },
        
        createLoadingOverlay: function() {
            if (!document.querySelector('.loading-overlay')) {
                const overlay = document.createElement('div');
                overlay.className = 'loading-overlay';
                overlay.innerHTML = `
                    <div class="loading-content">
                        <div class="spinner"></div>
                        <p>กำลังประมวลผล...</p>
                    </div>
                `;
                overlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(255, 255, 255, 0.9);
                    display: none;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                `;
                document.body.appendChild(overlay);
            }
        },
        
        show: function(message = 'กำลังประมวลผล...') {
            const overlay = document.querySelector('.loading-overlay');
            if (overlay) {
                const messageElement = overlay.querySelector('p');
                if (messageElement) {
                    messageElement.textContent = message;
                }
                overlay.style.display = 'flex';
            }
        },
        
        hide: function() {
            const overlay = document.querySelector('.loading-overlay');
            if (overlay) {
                overlay.style.display = 'none';
            }
        }
    },
    
    // Data Tables
    setupDataTables: function() {
        const tables = document.querySelectorAll('.data-table');
        
        tables.forEach(table => {
            // Add sorting functionality
            const headers = table.querySelectorAll('th[data-sort]');
            headers.forEach(header => {
                header.style.cursor = 'pointer';
                header.addEventListener('click', () => {
                    this.sortTable(table, header);
                });
            });
            
            // Add row hover effects
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.addEventListener('mouseenter', () => {
                    row.style.backgroundColor = 'var(--gray-50)';
                });
                
                row.addEventListener('mouseleave', () => {
                    row.style.backgroundColor = '';
                });
            });
        });
    },
    
    sortTable: function(table, header) {
        const column = Array.from(header.parentNode.children).indexOf(header);
        const rows = Array.from(table.querySelector('tbody').rows);
        const currentSort = header.dataset.sort;
        
        rows.sort((a, b) => {
            const aText = a.cells[column].textContent.trim();
            const bText = b.cells[column].textContent.trim();
            
            if (currentSort === 'asc') {
                return bText.localeCompare(aText, 'th');
            } else {
                return aText.localeCompare(bText, 'th');
            }
        });
        
        // Update sort indicator
        table.querySelectorAll('th').forEach(th => th.classList.remove('sort-asc', 'sort-desc'));
        header.classList.add(currentSort === 'asc' ? 'sort-desc' : 'sort-asc');
        header.dataset.sort = currentSort === 'asc' ? 'desc' : 'asc';
        
        // Reorder rows
        const tbody = table.querySelector('tbody');
        rows.forEach(row => tbody.appendChild(row));
    },
    
    // Search Functionality
    setupSearch: function() {
        const searchInputs = document.querySelectorAll('.search-input');
        
        searchInputs.forEach(input => {
            let timeout;
            
            input.addEventListener('input', () => {
                clearTimeout(timeout);
                
                timeout = setTimeout(() => {
                    this.performSearch(input);
                }, 300);
            });
        });
    },
    
    performSearch: function(input) {
        const query = input.value.toLowerCase().trim();
        const target = document.querySelector(input.dataset.target);
        
        if (!target) return;
        
        const items = target.querySelectorAll(input.dataset.items || 'tr');
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            
            if (text.includes(query)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    },
    
    // Date Pickers
    setupDatePickers: function() {
        const dateInputs = document.querySelectorAll('input[type="date"]');
        
        dateInputs.forEach(input => {
            // Format date for Thai locale
            input.addEventListener('change', () => {
                const value = input.value;
                if (value) {
                    const date = new Date(value);
                    const thaiDate = date.toLocaleDateString('th-TH');
                    
                    // Update display if there's a display element
                    const display = document.querySelector(`[data-display="${input.name}"]`);
                    if (display) {
                        display.textContent = thaiDate;
                    }
                }
            });
        });
    },
    
    // File Upload Handling
    setupFileUploads: function() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.handleFileUpload(e.target);
            });
        });
    },
    
    handleFileUpload: function(input) {
        const files = input.files;
        const maxSize = parseInt(input.dataset.maxSize) || 10485760; // 10MB default
        const allowedTypes = input.dataset.allowedTypes ? input.dataset.allowedTypes.split(',') : [];
        
        Array.from(files).forEach(file => {
            // Check file size
            if (file.size > maxSize) {
                this.showAlert('warning', `ไฟล์ ${file.name} มีขนาดเกินที่กำหนด`);
                input.value = '';
                return;
            }
            
            // Check file type
            if (allowedTypes.length > 0) {
                const fileExt = file.name.split('.').pop().toLowerCase();
                if (!allowedTypes.includes(fileExt)) {
                    this.showAlert('warning', `ไฟล์ ${file.name} เป็นประเภทที่ไม่อนุญาต`);
                    input.value = '';
                    return;
                }
            }
        });
        
        // Update file display
        this.updateFileDisplay(input);
    },
    
    updateFileDisplay: function(input) {
        const display = document.querySelector(`[data-file-display="${input.name}"]`);
        if (display && input.files.length > 0) {
            const fileNames = Array.from(input.files).map(file => file.name);
            display.textContent = fileNames.join(', ');
        }
    },
    
    // Tooltips
    setupTooltips: function() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', (e) => {
                this.showTooltip(e.target, e.target.dataset.tooltip);
            });
            
            element.addEventListener('mouseleave', (e) => {
                this.hideTooltip();
            });
        });
    },
    
    showTooltip: function(element, text) {
        let tooltip = document.querySelector('.tooltip-custom');
        
        if (!tooltip) {
            tooltip = document.createElement('div');
            tooltip.className = 'tooltip-custom';
            tooltip.style.cssText = `
                position: absolute;
                background: var(--gray-800);
                color: white;
                padding: 0.5rem;
                border-radius: var(--border-radius);
                font-size: 0.875rem;
                z-index: 1000;
                pointer-events: none;
                opacity: 0;
                transition: opacity 0.3s;
            `;
            document.body.appendChild(tooltip);
        }
        
        tooltip.textContent = text;
        
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
        tooltip.style.opacity = '1';
    },
    
    hideTooltip: function() {
        const tooltip = document.querySelector('.tooltip-custom');
        if (tooltip) {
            tooltip.style.opacity = '0';
        }
    },
    
    // Utility Functions
    utils: {
        formatNumber: function(num, decimals = 2) {
            return Number(num).toLocaleString('th-TH', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        },
        
        formatDate: function(dateString, format = 'th') {
            const date = new Date(dateString);
            
            if (format === 'th') {
                return date.toLocaleDateString('th-TH', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
            
            return date.toLocaleDateString('th-TH');
        },
        
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },
        
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
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    InventionVote.init();
});

// Global functions for backward compatibility
function showAlert(type, message, autoHide = true) {
    InventionVote.showAlert(type, message, autoHide);
}

function showLoading(message) {
    InventionVote.loadingManager.show(message);
}

function hideLoading() {
    InventionVote.loadingManager.hide();
}

function openModal(selector) {
    InventionVote.openModal(selector);
}

function closeModal(selector) {
    const modal = typeof selector === 'string' ? document.querySelector(selector) : selector;
    InventionVote.closeModal(modal);
}