/**
 * AJAX Form Handler
 *
 * This script converts regular form submissions into AJAX requests
 * to prevent page reloads and provide a seamless user experience.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Set up CSRF token for all fetch requests
    setupFetchInterceptor();

    // Initialize AJAX form handling
    initAjaxForms();

    // Re-initialize when content changes (for dynamically added forms)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                initAjaxForms();
            }
        });
    });

    // Observe the entire document for changes
    observer.observe(document.body, { childList: true, subtree: true });
});

/**
 * Set up a global fetch interceptor to add CSRF token to all requests
 */
function setupFetchInterceptor() {
    // Get the CSRF token from the meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (csrfToken) {
        // Set up CSRF token for all fetch requests
        const originalFetch = window.fetch;
        window.fetch = function(url, options = {}) {
            // Only add the X-CSRF-TOKEN header for same-origin requests
            if (new URL(url, window.location.href).origin === window.location.origin) {
                options.headers = options.headers || {};

                // Don't override if the header is already set
                if (!options.headers['X-CSRF-TOKEN'] && !options.headers['x-csrf-token']) {
                    options.headers['X-CSRF-TOKEN'] = csrfToken;
                }
            }

            return originalFetch(url, options);
        };

        console.log('CSRF token set up for all fetch requests');
    } else {
        console.warn('CSRF token not found. AJAX requests may fail for POST, PUT, DELETE methods.');
    }
}

/**
 * Initialize AJAX form handling for all forms with data-ajax attribute
 */
function initAjaxForms() {
    // Find all forms with data-ajax attribute
    document.querySelectorAll('form[data-ajax]').forEach(function(form) {
        // Skip if already initialized
        if (form.dataset.ajaxInitialized === 'true') return;

        // Skip if explicitly marked to not use AJAX
        if (form.dataset.noAjax === 'true') {
            console.log('Skipping AJAX handling for form with data-no-ajax attribute', form);
            return;
        }

        // Mark as initialized
        form.dataset.ajaxInitialized = 'true';

        // Add submit event listener
        form.addEventListener('submit', handleAjaxFormSubmit);
    });

    // Also check for forms with data-no-ajax attribute and remove any existing event listeners
    document.querySelectorAll('form[data-no-ajax="true"]').forEach(function(form) {
        console.log('Found form with data-no-ajax attribute', form);
        // Remove the data-ajax attribute if it exists
        if (form.hasAttribute('data-ajax')) {
            form.removeAttribute('data-ajax');
        }

        // Mark as not initialized for AJAX
        form.dataset.ajaxInitialized = 'false';

        // Remove the submit event listener if it was added
        form.removeEventListener('submit', handleAjaxFormSubmit);
    });
}

/**
 * Handle form submission via AJAX
 */
function handleAjaxFormSubmit(event) {
    const form = event.target;

    // Check if the form has the data-no-ajax attribute
    if (form.dataset.noAjax === 'true') {
        console.log('Form has data-no-ajax attribute, allowing normal submission');
        return true; // Allow normal form submission
    }

    // Prevent default form submission for AJAX handling
    event.preventDefault();

    const url = form.action;
    const method = form.method.toUpperCase();
    const formData = new FormData(form);

    // Show loading indicator
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton ? submitButton.innerHTML : null;

    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    }

    // Create the fetch options
    const fetchOptions = {
        method: method,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'same-origin'
    };

    // Add body data for POST, PUT, PATCH methods
    if (method !== 'GET') {
        fetchOptions.body = formData;
    }

    // Send the AJAX request
    fetch(url, fetchOptions)
        .then(response => {
            // Check if the response is a redirect
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }

            // Parse the response
            const contentType = response.headers.get('Content-Type');
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(data => ({ data, response }));
            } else {
                return response.text().then(text => ({ text, response }));
            }
        })
        .then(({ data, text, response }) => {
            // Handle the response
            if (response.ok) {
                if (data) {
                    // Handle JSON response
                    handleSuccessResponse(form, data);
                } else if (text) {
                    // Handle HTML response (rare case)
                    console.log('Received HTML response');
                }
            } else {
                // Handle error response
                handleErrorResponse(form, data || { message: 'An error occurred' });
            }
        })
        .catch(error => {
            console.error('AJAX request failed:', error);
            showToast('error', 'Request failed. Please try again.');
        })
        .finally(() => {
            // Restore submit button
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });
}

/**
 * Handle successful form submission
 */
function handleSuccessResponse(form, data) {
    // Check for success message
    if (data.message) {
        showToast('success', data.message);
    }

    // Check if we need to reset the form
    if (form.dataset.ajaxReset === 'true') {
        form.reset();
    }

    // Check if we need to close a modal
    if (form.closest('.modal') && typeof bootstrap !== 'undefined') {
        const modalElement = form.closest('.modal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    }

    // Check if we need to refresh a table or list
    if (form.dataset.ajaxRefresh) {
        const refreshSelector = form.dataset.ajaxRefresh;
        const elementToRefresh = document.querySelector(refreshSelector);

        if (elementToRefresh && data.html) {
            elementToRefresh.innerHTML = data.html;
        } else if (form.dataset.ajaxReload === 'true') {
            // If no specific HTML was provided but reload is requested
            // Reload the current page without showing the reload
            window.location.reload();
        }
    }

    // Trigger custom event for additional handling
    const event = new CustomEvent('ajax:success', {
        detail: { form: form, data: data }
    });
    document.dispatchEvent(event);
}

/**
 * Handle error response
 */
function handleErrorResponse(form, data) {
    // Show error message
    if (data.message) {
        showToast('error', data.message);
    }

    // Handle validation errors
    if (data.errors) {
        // Clear previous error messages
        form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });

        form.querySelectorAll('.invalid-feedback').forEach(el => {
            el.remove();
        });

        // Add new error messages
        Object.keys(data.errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');

                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = data.errors[field][0];

                input.parentNode.appendChild(errorDiv);
            }
        });
    }

    // Trigger custom event for additional handling
    const event = new CustomEvent('ajax:error', {
        detail: { form: form, data: data }
    });
    document.dispatchEvent(event);
}

/**
 * Show a toast notification
 */
function showToast(type, message) {
    // Check if we have a toast container
    let toastContainer = document.querySelector('.toast-container');

    // Create one if it doesn't exist
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }

    // Create the toast element
    const toastElement = document.createElement('div');
    toastElement.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
    toastElement.setAttribute('role', 'alert');
    toastElement.setAttribute('aria-live', 'assertive');
    toastElement.setAttribute('aria-atomic', 'true');

    // Create the toast content
    toastElement.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    // Add the toast to the container
    toastContainer.appendChild(toastElement);

    // Initialize and show the toast
    if (typeof bootstrap !== 'undefined') {
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000
        });
        toast.show();
    }
}
