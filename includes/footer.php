<?php
// includes/footer.php
?>
    <?php if (!isset($hide_container) || !$hide_container): ?>
    </div>
    <?php endif; ?>

    <?php if (isset($show_footer) && $show_footer): ?>
    <footer class="bg-light-custom border-top mt-auto py-3">
        <div class="container-fluid px-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small class="text-muted">
                        © <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> v<?php echo SITE_VERSION; ?>
                    </small>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        เวลาปัจจุบัน: <?php echo date('d/m/Y H:i:s'); ?>
                    </small>
                </div>
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Common JavaScript functions -->
    <script>
        // CSRF Token for AJAX requests
        const csrfToken = '<?php echo generate_csrf_token(); ?>';
        
        // Show loading spinner
        function showLoading() {
            const spinner = document.createElement('div');
            spinner.className = 'spinner-overlay';
            spinner.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
            `;
            document.body.appendChild(spinner);
        }
        
        // Hide loading spinner
        function hideLoading() {
            const spinner = document.querySelector('.spinner-overlay');
            if (spinner) {
                spinner.remove();
            }
        }
        
        // Show alert message
        function showAlert(message, type = 'info', duration = 5000) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto dismiss after duration
            if (duration > 0) {
                setTimeout(() => {
                    if (alertDiv && alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, duration);
            }
        }
        
        // Confirm dialog
        function confirmAction(message, callback) {
            if (confirm(message)) {
                callback();
            }
        }
        
        // Format number with Thai locale
        function formatNumber(number, decimals = 0) {
            return new Intl.NumberFormat('th-TH', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            }).format(number);
        }
        
        // Format Thai date
        function formatThaiDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    if (bsAlert) {
                        bsAlert.close();
                    }
                }, 5000);
            });
        });
        
        // Form validation helpers
        function validateForm(formId) {
            const form = document.getElementById(formId);
            if (!form) return false;
            
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            return isValid;
        }
        
        // Auto-resize textarea
        function autoResizeTextarea() {
            const textareas = document.querySelectorAll('textarea[data-auto-resize]');
            textareas.forEach(function(textarea) {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            });
        }
        
        // Initialize auto-resize on page load
        document.addEventListener('DOMContentLoaded', autoResizeTextarea);
        
        // Session timeout warning
        <?php if (is_logged_in()): ?>
        let sessionTimeout = <?php echo SESSION_TIMEOUT; ?> * 1000; // Convert to milliseconds
        let warningTime = sessionTimeout - (5 * 60 * 1000); // Warning 5 minutes before timeout
        
        setTimeout(function() {
            showAlert('เซสชันของคุณจะหมดอายุในอีก 5 นาที กรุณาบันทึกงานของคุณ', 'warning', 0);
        }, warningTime);
        
        setTimeout(function() {
            showAlert('เซสชันหมดอายุ กำลังนำท่านไปยังหน้าเข้าสู่ระบบ...', 'danger', 3000);
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 3000);
        }, sessionTimeout);
        <?php endif; ?>
    </script>
    
    <?php if (isset($additional_js)) echo $additional_js; ?>
</body>
</html>