import './bootstrap';

// Helper function to format time (similar to layout files)
function formatTimeAgo(timestamp) {
    const now = new Date();
    const past = new Date(timestamp);
    const msPerMinute = 60 * 1000;
    const msPerHour = msPerMinute * 60;
    const msPerDay = msPerHour * 24;
    const elapsed = now - past;

    if (elapsed < msPerMinute) {
            return Math.round(elapsed/1000) + ' seconds ago';
    } else if (elapsed < msPerHour) {
            return Math.round(elapsed/msPerMinute) + ' minutes ago';
    } else if (elapsed < msPerDay ) {
            return Math.round(elapsed/msPerHour ) + ' hours ago';
    } else {
            return past.toLocaleDateString() + ' ' + past.toLocaleTimeString();
    }
}

// Helper function to get icon (similar to layout files)
function getNotificationIcon(type) {
    const baseType = type.split('\\\\').pop().replace('Notification', '').toLowerCase();
    if (baseType.includes('deadline')) return '<div class="notification-icon deadline"><i class="fas fa-clock"></i></div>';
    if (baseType.includes('reportremarks')) return '<div class="notification-icon report"><i class="fas fa-file-alt"></i></div>';
    if (baseType.includes('newsubmission')) return '<div class="notification-icon submission"><i class="fas fa-file-upload"></i></div>'; // Ensure 'submission' class exists in CSS if needed
    return '<div class="notification-icon system"><i class="fas fa-bell"></i></div>';
}

// Function to add a new notification to the UI
function addNewNotificationToUI(notification) {
    console.log('Adding new notification to UI:', notification);

    // Try to find notification list (different selectors for different layouts)
    let notificationList = document.querySelector('#notificationList'); // Barangay layout
    if (!notificationList) {
        notificationList = document.querySelector('.notification-list'); // Facilitator/Admin layout
    }

    if (!notificationList) {
        console.warn('Notification list not found in DOM');
        return;
    }

    // Remove "No notifications" message if present
    const noNotificationsMsg = notificationList.querySelector('.notification-empty, .text-center');
    if (noNotificationsMsg && (
        noNotificationsMsg.textContent.includes('No notifications') ||
        noNotificationsMsg.textContent.includes('No notifications found')
    )) {
        notificationList.innerHTML = '';
    }

    const message = notification.message || 'New notification';
    const redirectUrl = notification.redirect_url || '#';
    let title = 'Notification';

    // Determine title based on notification type
    if (notification.notification_type === 'report_remarks') {
        title = 'Report Remarks Added';
    } else if (notification.notification_type === 'upcoming_deadline') {
        title = 'Upcoming Deadline';
    } else if (notification.notification_type === 'new_submission_received') {
        title = 'New Submission';
    } else if (notification.full_report_title) {
        title = notification.full_report_title; // from ReportRemarks
    } else if (notification.report_name) {
        title = notification.report_name; // from NewSubmission
    } else if (notification.report_type_name) {
        title = notification.report_type_name; // from UpcomingDeadline
    }

    // Create notification item
    const item = document.createElement('div');
    item.className = 'notification-item unread';
    item.dataset.id = notification.id || 'temp-' + Date.now();
    item.dataset.type = notification.notification_type || 'general';
    item.dataset.redirectUrl = redirectUrl;

    // Use layout-specific functions if available
    if (document.querySelector('.barangay-header') && typeof window.addNewNotificationToBarangayUI === 'function') {
        // Use barangay-specific function
        window.addNewNotificationToBarangayUI(notification);
        return;
    }

    // Fallback: Different HTML structure for different layouts
    if (document.querySelector('.barangay-header')) {
        // Barangay layout structure
        const iconClass = getNotificationIconClass(notification.notification_type);
        item.innerHTML = `
            <div class="notification-icon ${notification.notification_type || 'system'}">
                <i class="fas ${iconClass}"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">${title}</div>
                <div class="notification-message">${message}</div>
                <div class="notification-meta">
                    <span class="notification-time">${formatTimeAgo(notification.timestamp || new Date().toISOString())}</span>
                    ${notification.can_update ? '<span class="notification-badge">Action Required</span>' : ''}
                </div>
            </div>
        `;
    } else {
        // Facilitator/Admin layout structure
        item.innerHTML = `
            ${getNotificationIcon(notification.broadcast_type || notification.notification_type)}
            <div class="notification-content">
                <div class="notification-title">${title}</div>
                <div class="notification-message">${message}</div>
                <div class="notification-time">${formatTimeAgo(notification.timestamp || new Date().toISOString())}</div>
            </div>
        `;
    }

    notificationList.prepend(item); // Add to the top

    // Update unread count
    if (typeof window.fetchUnreadCount === 'function') {
        window.fetchUnreadCount();
    } else if (typeof fetchUnreadCount === 'function') {
        fetchUnreadCount();
    }

    // Show toast notification for better UX
    showNotificationToast(title, message, notification.notification_type);

    console.info('Notification added to UI:', title, notification);
}

// Helper function to get icon class for barangay layout
function getNotificationIconClass(type) {
    switch(type) {
        case 'report_remarks':
        case 'report':
            return 'fa-file-alt';
        case 'upcoming_deadline':
        case 'deadline':
            return 'fa-clock';
        case 'new_submission_received':
        case 'submission':
            return 'fa-file-upload';
        case 'announcement':
            return 'fa-bullhorn';
        case 'user':
            return 'fa-user';
        default:
            return 'fa-bell';
    }
}

// Function to show toast notification
function showNotificationToast(title, message, type) {
    // Check if SweetAlert2 is available
    if (typeof Swal !== 'undefined') {
        const icon = type === 'report_remarks' ? 'info' : 'success';
        Swal.fire({
            title: title,
            text: message,
            icon: icon,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    } else {
        // Fallback to browser notification if available
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(title, {
                body: message,
                icon: '/favicon.ico'
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const userIdMeta = document.querySelector("meta[name='user-id']");
    const userId = userIdMeta ? userIdMeta.getAttribute('content') : null;

    if (userId && window.Echo) {
        console.log('Echo: Listening for notifications for user: ' + userId);

        window.Echo.private('App.Models.User.' + userId)
            .listen('.report.remarks.added', (e) => {
                console.log('Echo: Report Remarks Added:', e);
                addNewNotificationToUI(e); 
            })
            .listen('.deadline.upcoming', (e) => {
                console.log('Echo: Upcoming Deadline:', e);
                addNewNotificationToUI(e); 
            })
            .listen('.submission.new.received', (e) => {
                console.log('Echo: New Submission Received:', e);
                addNewNotificationToUI(e); 
            })
            .error((error) => {
                console.error('Echo channel error:', error);
            });

    } else {
        if (!userId) {
            console.log('Echo: User ID not found. Real-time notifications disabled.');
        }
        if (!window.Echo) {
            console.log('Echo: Laravel Echo not initialized. Real-time notifications disabled.');
        }
    }
});

// Expose fetchUnreadCount globally if it's defined in one of the layouts for app.js to call
// This is a workaround. A better approach is custom events or a shared state manager.
if (typeof fetchUnreadCount === 'function' && typeof window.fetchUnreadCountGlobal === 'undefined') {
    window.fetchUnreadCountGlobal = fetchUnreadCount;
}
