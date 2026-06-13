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

    <!-- Success Notification Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <div class="icon-circle bg-success mb-3">OK</div>
                    <h5 class="fw-bold mb-2" id="successModalTitle">Success!</h5>
                    <p class="text-muted mb-0" id="successModalMessage">Your action was completed successfully.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                    <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal" id="successModalBtn">OK</button>
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
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    
    <?php if (isset($extra_scripts)): ?>
        <?php echo $extra_scripts; ?>
    <?php endif; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php $flash = getFlashMessage(); if ($flash && $flash['type'] === 'success'): ?>
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            document.getElementById('successModalTitle').textContent = 'Success!';
            document.getElementById('successModalMessage').textContent = '<?php echo addslashes($flash["message"]); ?>';
            successModal.show();
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
