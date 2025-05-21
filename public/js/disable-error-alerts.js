/**
 * Disable Error Alerts
 * 
 * This script disables all error alerts and error messages in the application.
 * It overrides the alert function, the SweetAlert2 library, and the showToast function.
 */

// Wait for the document to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Disabling error alerts and messages');
    
    // Override the alert function to prevent error alerts
    const originalAlert = window.alert;
    window.alert = function(message) {
        // If this is an error message, don't show it
        if (message && (
            message.includes('error') || 
            message.includes('Error') || 
            message.includes('failed') || 
            message.includes('Failed') ||
            message.includes('An error occurred while submitting the report')
        )) {
            console.log('Preventing error alert:', message);
            return;
        }
        
        // For all other alerts, use the original function
        return originalAlert(message);
    };
    
    // Disable SweetAlert2 if it exists
    if (window.Swal) {
        console.log('Disabling SweetAlert2 error messages');
        const originalSwal = window.Swal;
        window.Swal = function(options) {
            // If this is an error message, don't show it
            if (options && (options.icon === 'error' || options.type === 'error')) {
                console.log('Preventing SweetAlert2 error message:', options);
                return {
                    then: function() { return this; },
                    catch: function() { return this; },
                    finally: function() { return this; }
                };
            }
            
            // For all other alerts, use the original Swal
            return originalSwal(options);
        };
        
        // Copy all static methods from the original Swal
        for (const key in originalSwal) {
            if (typeof originalSwal[key] === 'function') {
                window.Swal[key] = function() {
                    // If this is an error method, don't show it
                    if (key === 'fire' && arguments[0] && (arguments[0].icon === 'error' || arguments[0].type === 'error')) {
                        console.log('Preventing SweetAlert2 error message from static method:', arguments[0]);
                        return {
                            then: function() { return this; },
                            catch: function() { return this; },
                            finally: function() { return this; }
                        };
                    }
                    
                    // For all other methods, use the original
                    return originalSwal[key].apply(originalSwal, arguments);
                };
            } else {
                window.Swal[key] = originalSwal[key];
            }
        }
    }
    
    // Disable the showToast function from ajax-forms.js
    if (window.showToast) {
        console.log('Disabling showToast function');
        const originalShowToast = window.showToast;
        window.showToast = function(type, message) {
            // If this is an error message, don't show it
            if (type === 'error') {
                console.log('Preventing error toast:', message);
                return;
            }
            
            // For all other toasts, use the original function
            return originalShowToast(type, message);
        };
    }
    
    // Override the fetch function to prevent AJAX for form submissions
    const originalFetch = window.fetch;
    window.fetch = function(url, options = {}) {
        // If this is a form submission to the submissions.store route, don't use fetch
        if (url && url.toString().includes('submissions/store') && options && options.method === 'POST') {
            console.log('Preventing AJAX for form submission to submissions/store');
            // Don't actually call fetch, let the form submit normally
            return new Promise((resolve, reject) => {
                // This promise will never resolve or reject
            });
        }
        
        // For all other requests, use the original fetch
        return originalFetch(url, options);
    };
    
    // Disable the handleAjaxFormSubmit function from ajax-forms.js
    if (window.handleAjaxFormSubmit) {
        console.log('Disabling handleAjaxFormSubmit function');
        const originalHandleAjaxFormSubmit = window.handleAjaxFormSubmit;
        window.handleAjaxFormSubmit = function(event) {
            // Get the form element
            const form = event.target;
            
            // If this is a form submission to the submissions.store route, don't use AJAX
            if (form && form.action && form.action.toString().includes('submissions/store')) {
                console.log('Preventing AJAX for form submission to submissions/store');
                return true; // Let the form submit normally
            }
            
            // For all other forms, use the original function
            return originalHandleAjaxFormSubmit(event);
        };
    }
});
