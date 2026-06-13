        </div>

    <footer class="footer mt-auto py-3 bg-light">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <span class="text-muted">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</span>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-muted">Version 1.0.0</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="appToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <span id="toastIcon"></span>
                    <span id="toastMessage"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <div class="modal-icon-circle bg-danger-subtle mb-3">
                        <div class="icon-circle bg-danger">?</div>
                    </div>
                    <h5 class="fw-bold mb-2">Are you sure you want to logout?</h5>
                    <p class="text-muted mb-0">You will be signed out of your account and redirected to the login page.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <a href="<?php echo $base ?? ''; ?>logout.php" class="btn btn-danger px-3">Yes, Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Resolve Confirmation Modal -->
    <div class="modal fade" id="resolveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <div class="icon-circle bg-success mb-3">Done</div>
                    <h5 class="fw-bold mb-2">Ticket Resolved!</h5>
                    <p class="text-muted mb-0">This ticket has been marked as resolved. The user will be notified.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                    <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">Got it!</button>
                </div>
            </div>
        </div>
    </div>

    <style>
    .icon-circle {
        width: 72px; height: 72px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.25rem; font-weight: 700; color: #fff;
        animation: successPop 0.4s ease;
    }
    .bg-danger-subtle .icon-circle { background: #e63757; }
    .bg-success { background: #17a673; }
    .bg-danger { background: #e63757; }
    .modal-content { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
    #logoutModal .btn-danger { background: #e63757; border-color: #e63757; }
    #logoutModal .btn-danger:hover { background: #c4293f; border-color: #c4293f; }
    @keyframes successPop {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); opacity: 1; }
    }

    #appToast {
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        min-width: 300px;
    }
    #appToast.toast-success {
        background-color: #d4f5e9;
        color: #0d6840;
        border-left: 4px solid #17a673;
    }
    #appToast.toast-danger {
        background-color: #fde2e8;
        color: #e63757;
        border-left: 4px solid #e63757;
    }
    #appToast.toast-warning {
        background-color: #fef3d6;
        color: #b8860b;
        border-left: 4px solid #e6a817;
    }
    #appToast.toast-info {
        background-color: #e8f0fe;
        color: #2c7be5;
        border-left: 4px solid #2c7be5;
    }
    #toastIcon {
        margin-right: 8px;
        font-weight: 700;
    }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    
    <?php if (isset($extra_scripts)): ?>
        <?php echo $extra_scripts; ?>
    <?php endif; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php $flash = getFlashMessage(); if ($flash): ?>
            (function() {
                var toast = document.getElementById('appToast');
                var toastMsg = document.getElementById('toastMessage');
                var toastIcon = document.getElementById('toastIcon');

                toast.className = 'toast align-items-center border-0 toast-<?php echo $flash["type"]; ?>';
                
                var icons = {
                    success: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
                    danger: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
                    warning: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
                    info: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
                };

                toastIcon.innerHTML = icons['<?php echo $flash["type"]; ?>'] || icons.info;
                toastMsg.textContent = '<?php echo addslashes($flash["message"]); ?>';

                var bsToast = new bootstrap.Toast(toast, { delay: 4000 });
                bsToast.show();
            })();
        <?php endif; ?>

        <?php if (!empty($show_resolve_modal)): ?>
            setTimeout(function() {
                var resolveModal = new bootstrap.Modal(document.getElementById('resolveModal'));
                resolveModal.show();
            }, 300);
        <?php endif; ?>
    });
    </script>
</body>
</html>
