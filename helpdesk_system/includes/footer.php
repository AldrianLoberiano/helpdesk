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
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
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
                        <div class="icon-circle bg-danger">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        </div>
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
                    <div class="icon-circle bg-success mb-3">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <h5 class="fw-bold mb-2">Ticket Resolved!</h5>
                    <p class="text-muted mb-0">This ticket has been marked as resolved. The user will be notified.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                    <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">Got it!</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification View Modal -->
    <div class="modal fade" id="notifModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="notif-modal-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2d5a8e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        </span>
                        <h6 class="modal-title fw-bold" id="notifModalTitle">Notification</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="text-muted small mb-2" id="notifModalTime"></p>
                    <div class="notif-modal-body" id="notifModalMessage"></div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Close</button>
                    <a href="#" class="btn btn-primary px-3" id="notifModalLink">Go to Ticket</a>
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
    .notif-modal-icon {
        width: 36px; height: 36px; border-radius: 50%;
        background: #e8f0fe; display: inline-flex;
        align-items: center; justify-content: center; flex-shrink: 0;
    }
    .notif-modal-body {
        background: #f8f9fc; border-radius: 10px; padding: 1rem;
        border: 1px solid #e4e7ec; font-size: 0.9rem; line-height: 1.7;
        color: #1a1d21; white-space: pre-wrap;
    }
    </style>

    <script src="<?php echo SITE_URL; ?>/assets/js/bootstrap.bundle.min.js"></script>
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

                toast.className = 'toast align-items-center border-0 toast-<?php echo addslashes($flash["type"]); ?>';
                
                var icons = {
                    success: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
                    danger: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
                    warning: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
                    info: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
                };

                toastIcon.innerHTML = icons['<?php echo addslashes($flash["type"]); ?>'] || icons.info;
                toastMsg.textContent = <?php echo json_encode($flash["message"]); ?>;

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

        // Notification modal handler
        var notifModal = new bootstrap.Modal(document.getElementById('notifModal'));
        document.querySelectorAll('.notification-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var title = this.dataset.notifTitle;
                var message = this.dataset.notifMessage;
                var time = this.dataset.notifTime;
                var link = this.dataset.notifLink;
                var notifId = this.dataset.notifId;
                var isRead = this.dataset.notifRead;

                document.getElementById('notifModalTitle').textContent = title;
                document.getElementById('notifModalMessage').textContent = message;
                document.getElementById('notifModalTime').textContent = time;
                var goBtn = document.getElementById('notifModalLink');
                if (link && link !== '#') {
                    goBtn.href = link;
                    goBtn.style.display = '';
                } else {
                    goBtn.style.display = 'none';
                }

                notifModal.show();

                // Mark as read
                if (isRead === '0') {
                    fetch(window.SITE_BASE + '/mark_notifications_read.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'notif_id=' + notifId
                    }).then(function(r) { return r.json(); }).then(function(data) {
                        if (data.success) {
                            var badge = document.querySelector('.notif-badge');
                            if (badge) {
                                var newCount = parseInt(badge.textContent) - 1;
                                if (newCount <= 0) {
                                    badge.remove();
                                } else {
                                    badge.textContent = newCount;
                                }
                            }
                            this.closest('.notification-item').classList.remove('unread');
                        }
                    }.bind(this)).catch(function() {});
                }
            });
        });
    });
    </script>
</body>
</html>
