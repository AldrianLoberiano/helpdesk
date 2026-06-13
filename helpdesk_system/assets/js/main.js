/**
 * IT Helpdesk Ticketing System - JavaScript
 */

// Compute base URL for AJAX calls
(function() {
    var path = window.location.pathname;
    var parts = path.split('/').filter(Boolean);
    var baseParts = [];
    // Find 'helpdesk_system' in the path and use everything up to it
    for (var i = 0; i < parts.length; i++) {
        baseParts.push(parts[i]);
        if (parts[i] === 'helpdesk_system') break;
    }
    window.SITE_BASE = '/' + baseParts.join('/');
})();

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Mark all notifications as read
    var markAllReadBtn = document.querySelector('.mark-all-read');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // AJAX call to mark all as read
            fetch(window.SITE_BASE + '/mark_notifications_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    var unreadItems = document.querySelectorAll('.notification-item.unread');
                    unreadItems.forEach(function(item) {
                        item.classList.remove('unread');
                    });
                    var badge = document.querySelector('.badge.bg-danger');
                    if (badge) {
                        badge.remove();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // Confirm delete actions
    var deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    // File upload preview
    var fileInput = document.querySelector('input[type="file"][multiple]');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            var files = e.target.files;
            var preview = document.getElementById('file-preview');
            if (preview) {
                preview.innerHTML = '';
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    var fileItem = document.createElement('div');
                    fileItem.className = 'file-item mb-2 p-2 bg-light rounded';
                    fileItem.innerHTML = '<i class="fas fa-file me-2"></i>' + file.name + 
                                        ' <span class="text-muted">(' + formatFileSize(file.size) + ')</span>';
                    preview.appendChild(fileItem);
                }
            }
        });
    }

    // Format file size helper
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Search functionality
    var searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    }

    // Status filter change
    var statusSelect = document.querySelector('select[name="status"]');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            this.closest('form').submit();
        });
    }

    // Priority filter change
    var prioritySelect = document.querySelector('select[name="priority"]');
    if (prioritySelect) {
        prioritySelect.addEventListener('change', function() {
            this.closest('form').submit();
        });
    }

    // Responsive table handling
    var tables = document.querySelectorAll('.table-responsive');
    tables.forEach(function(table) {
        if (table.scrollWidth > table.clientWidth) {
            table.style.overflowX = 'auto';
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + N for new ticket (Employee)
        if (e.ctrlKey && e.key === 'n') {
            var newTicketBtn = document.querySelector('a[href*="create_ticket"]');
            if (newTicketBtn) {
                e.preventDefault();
                window.location.href = newTicketBtn.href;
            }
        }
    });

    // Auto-refresh notifications every 60 seconds
    setInterval(function() {
        fetch(window.SITE_BASE + '/get_notification_count.php')
            .then(response => response.json())
            .then(data => {
                var badge = document.querySelector('.notif-badge');
                if (data.count > 0) {
                    if (!badge) {
                        if (bellIcon) {
                            var newBadge = document.createElement('span');
                            newBadge.className = 'badge bg-danger';
                            newBadge.textContent = data.count;
                            bellIcon.parentNode.appendChild(newBadge);
                        }
                    } else {
                        badge.textContent = data.count;
                    }
                } else if (badge) {
                    badge.remove();
                }
            })
            .catch(error => console.error('Error:', error));
    }, 60000);
});

// Function to confirm status change
function confirmStatusChange(newStatus) {
    return confirm('Are you sure you want to change the status to ' + newStatus + '?');
}

// Function to confirm ticket closure
function confirmCloseTicket() {
    return confirm('Are you sure you want to close this ticket? This action cannot be undone.');
}