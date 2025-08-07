</div>
        <!-- Page Content End -->
        
        <!-- Footer -->
        <footer class="main-footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="footer-info">
                            <strong><?php echo SYSTEM_NAME; ?></strong> Version <?php echo SYSTEM_VERSION; ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 text-md-right">
                        <div class="footer-links">
                            <span class="text-muted">
                                © <?php echo date('Y'); ?> สำนักงานการศึกษานอกระบบและการศึกษาตามอัธยาศัย
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </main>
    
    <!-- JavaScript -->
    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
    
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo BASE_URL . $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Custom scripts for this page -->
    <?php if (isset($customJS)): ?>
        <script><?php echo $customJS; ?></script>
    <?php endif; ?>
    
    <!-- Page-specific JavaScript -->
    <script>
    // Set CSRF token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Global AJAX setup
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        let [resource, config] = args;
        
        // Add CSRF token to POST requests
        if (config && config.method && config.method.toLowerCase() === 'post') {
            if (config.body instanceof FormData) {
                if (!config.body.has('csrf_token')) {
                    config.body.append('csrf_token', csrfToken);
                }
            }
        }
        
        return originalFetch.apply(this, args);
    };
    
    // Session timeout warning
    let sessionTimeout;
    let warningTimeout;
    
    function resetSessionTimer() {
        clearTimeout(sessionTimeout);
        clearTimeout(warningTimeout);
        
        // Warning 5 minutes before timeout
        warningTimeout = setTimeout(function() {
            if (confirm('เซสชันของคุณจะหมดอายุในอีก 5 นาที คุณต้องการขยายเวลาหรือไม่?')) {
                // Ping server to extend session
                fetch('<?php echo BASE_URL; ?>ajax/extend_session.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ csrf_token: csrfToken })
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resetSessionTimer();
                        showAlert('success', 'เซสชันได้รับการขยายเวลาแล้ว');
                    }
                });
            }
        }, <?php echo (SESSION_TIMEOUT - 300) * 1000; ?>); // 5 minutes before timeout
        
        // Auto logout
        sessionTimeout = setTimeout(function() {
            alert('เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่');
            window.location.href = '<?php echo BASE_URL; ?>logout.php';
        }, <?php echo SESSION_TIMEOUT * 1000; ?>);
    }
    
    // Start session timer
    resetSessionTimer();
    
    // Reset timer on user activity
    ['click', 'keypress', 'scroll', 'mousemove'].forEach(function(event) {
        document.addEventListener(event, resetSessionTimer, { passive: true });
    });
    
    // Auto-save functionality for forms
    const autoSaveForms = document.querySelectorAll('form[data-auto-save]');
    autoSaveForms.forEach(form => {
        const formData = {};
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                const key = `autosave_${form.id || 'form'}_${input.name}`;
                localStorage.setItem(key, input.value);
            });
            
            // Restore saved data
            const key = `autosave_${form.id || 'form'}_${input.name}`;
            const savedValue = localStorage.getItem(key);
            if (savedValue && !input.value) {
                input.value = savedValue;
            }
        });
        
        // Clear autosave on successful submit
        form.addEventListener('submit', function() {
            inputs.forEach(input => {
                const key = `autosave_${form.id || 'form'}_${input.name}`;
                localStorage.removeItem(key);
            });
        });
    });
    
    // Print functionality
    window.printPage = function() {
        window.print();
    };
    
    // Export functionality
    window.exportData = function(format, url) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        const formatInput = document.createElement('input');
        formatInput.type = 'hidden';
        formatInput.name = 'export_format';
        formatInput.value = format;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = csrfToken;
        
        form.appendChild(formatInput);
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    };
    
    // Back to top button
    const backToTopBtn = document.createElement('button');
    backToTopBtn.innerHTML = '↑';
    backToTopBtn.className = 'back-to-top';
    backToTopBtn.style.cssText = `
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 50px;
        height: 50px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.2rem;
        font-weight: bold;
        box-shadow: var(--shadow-lg);
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: var(--transition);
    `;
    
    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    
    document.body.appendChild(backToTopBtn);
    
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTopBtn.style.opacity = '1';
            backToTopBtn.style.visibility = 'visible';
        } else {
            backToTopBtn.style.opacity = '0';
            backToTopBtn.style.visibility = 'hidden';
        }
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+S to save (prevent default and trigger save if available)
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            const saveBtn = document.querySelector('[data-action="save"], .btn-save, button[type="submit"]');
            if (saveBtn) {
                saveBtn.click();
            }
        }
        
        // Escape to close modals
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                closeModal(openModal);
            }
        }
    });
    </script>
    
    <!-- Development/Debug Info (only in development) -->
    <?php if (ini_get('display_errors')): ?>
    <script>
    console.log('INVENTION-VOTE System Debug Info:');
    console.log('User ID: <?php echo $_SESSION['user_id'] ?? 'Not logged in'; ?>');
    console.log('User Type: <?php echo $_SESSION['user_type'] ?? 'None'; ?>');
    console.log('Current Page: <?php echo $currentPage ?? 'Unknown'; ?>');
    console.log('PHP Memory Usage: <?php echo round(memory_get_usage() / 1024 / 1024, 2); ?> MB');
    console.log('PHP Peak Memory: <?php echo round(memory_get_peak_usage() / 1024 / 1024, 2); ?> MB');
    console.log('Page Load Time: ' + (performance.now() / 1000).toFixed(3) + ' seconds');
    </script>
    <?php endif; ?>
</body>
</html>

<style>
/* Footer Styles */
.main-footer {
    background: var(--white);
    border-top: 1px solid var(--gray-200);
    padding: 2rem 0;
    margin-top: 3rem;
    color: var(--gray-600);
    font-size: 0.875rem;
}

.footer-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.footer-info strong {
    color: var(--primary-color);
    font-weight: 600;
}

.footer-links {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.footer-links a {
    color: var(--gray-500);
    text-decoration: none;
    transition: var(--transition);
}

.footer-links a:hover {
    color: var(--primary-color);
}

.back-to-top:hover {
    background: var(--primary-dark) !important;
    transform: translateY(-2px);
}

/* Loading states */
.loading {
    opacity: 0.6;
    pointer-events: none;
    position: relative;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid var(--gray-300);
    border-top: 2px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Print styles */
@media print {
    .main-footer,
    .back-to-top,
    .sidebar,
    .navbar,
    .alert,
    .btn,
    .pagination {
        display: none !important;
    }
    
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    
    .page-header {
        margin-bottom: 1rem !important;
        box-shadow: none !important;
        border: 1px solid #000 !important;
    }
    
    body {
        background: white !important;
        color: black !important;
        font-size: 12pt !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #000 !important;
        break-inside: avoid;
    }
    
    .table {
        border-collapse: collapse !important;
    }
    
    .table th,
    .table td {
        border: 1px solid #000 !important;
        padding: 0.25rem !important;
    }
    
    .page-title::after {
        content: " - หน้า " counter(page);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .main-footer {
        text-align: center;
        padding: 1rem 0;
    }
    
    .footer-links {
        margin-top: 0.5rem;
        justify-content: center;
    }
    
    .back-to-top {
        bottom: 1rem !important;
        right: 1rem !important;
        width: 45px !important;
        height: 45px !important;
    }
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
    .back-to-top {
        transition: none !important;
    }
    
    html {
        scroll-behavior: auto !important;
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .main-footer {
        border-top: 2px solid var(--gray-800);
    }
    
    .back-to-top {
        border: 2px solid white;
    }
}

/* Focus indicators for accessibility */
.back-to-top:focus-visible {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}
</style>