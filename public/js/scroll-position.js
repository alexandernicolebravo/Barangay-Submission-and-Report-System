/**
 * Smart Scroll Position Management
 *
 * This script:
 * 1. Forces scroll to top when navigating between different pages
 * 2. Maintains scroll position when using functionality within the same page
 */

(function() {
    'use strict';

    // Configuration
    const config = {
        keyPrefix: 'scrollPos_',       // Prefix for storage keys
        expireAfter: 5 * 60 * 1000     // Expire saved positions after 5 minutes
    };

    // Create a key for the current page
    const getPageKey = () => {
        return config.keyPrefix + window.location.pathname;
    };

    // Create a key for the current action (page + query params)
    const getActionKey = () => {
        return config.keyPrefix + window.location.pathname + window.location.search;
    };

    // Force scroll to top
    const scrollToTop = () => {
        window.scrollTo(0, 0);
    };

    // Save the current scroll position for same-page actions
    const saveScrollPosition = (key) => {
        try {
            sessionStorage.setItem(key, JSON.stringify({
                value: window.scrollY,
                timestamp: Date.now()
            }));
        } catch (e) {
            console.error('Failed to save scroll position:', e);
        }
    };

    // Get a saved scroll position
    const getSavedPosition = (key) => {
        try {
            const item = sessionStorage.getItem(key);
            if (!item) return null;

            const data = JSON.parse(item);

            // Check if the saved position has expired
            if (Date.now() - data.timestamp > config.expireAfter) {
                sessionStorage.removeItem(key);
                return null;
            }

            return data.value;
        } catch (e) {
            console.error('Failed to retrieve scroll position:', e);
            return null;
        }
    };

    // Clear a specific saved position
    const clearSavedPosition = (key) => {
        try {
            sessionStorage.removeItem(key);
        } catch (e) {
            console.error('Failed to clear scroll position:', e);
        }
    };

    // Set up event listeners for navigation between pages
    const setupNavigationListeners = () => {
        // Only add listeners to links that navigate to different pages
        document.querySelectorAll('a').forEach(link => {
            // Skip links that are for same-page actions
            if (link.getAttribute('href') === '#' ||
                link.getAttribute('href')?.startsWith('#') ||
                link.getAttribute('data-bs-toggle') === 'tab' ||
                link.getAttribute('data-bs-toggle') === 'pill' ||
                link.getAttribute('data-bs-toggle') === 'modal') {
                return;
            }

            // Skip links that point to the current page with different query params
            const linkUrl = link.getAttribute('href');
            if (linkUrl && linkUrl.startsWith('?')) {
                return;
            }

            // For links to different pages, force scroll to top
            link.addEventListener('click', function() {
                // Clear any saved positions for the current page
                clearSavedPosition(getPageKey());
                scrollToTop();
            });
        });
    };

    // Set up event listeners for same-page actions
    const setupSamePageListeners = () => {
        // For forms that submit to the same page (like filter forms)
        document.querySelectorAll('form').forEach(form => {
            // Only handle GET forms that submit to the same page
            if (form.method.toLowerCase() === 'get' &&
                (!form.action || form.action.includes(window.location.pathname))) {
                form.addEventListener('submit', function() {
                    saveScrollPosition(getPageKey());
                });
            }
        });

        // For filter inputs and selects that might trigger same-page actions
        document.querySelectorAll('input[type="search"], select[name]').forEach(el => {
            el.addEventListener('change', function() {
                if (el.closest('form')?.method?.toLowerCase() === 'get') {
                    saveScrollPosition(getPageKey());
                }
            });
        });
    };

    // Initialize
    const init = () => {
        // Check if we're coming back to the same page
        const referrer = document.referrer;
        const referrerPath = referrer ? new URL(referrer).pathname : '';
        const currentPath = window.location.pathname;

        if (referrerPath === currentPath) {
            // We're on the same page, possibly after a filter/search action
            // Try to restore the scroll position
            const savedPos = getSavedPosition(getPageKey());
            if (savedPos !== null) {
                // Restore the position
                window.scrollTo(0, parseInt(savedPos));

                // Clear the saved position
                clearSavedPosition(getPageKey());
            }
        } else {
            // We're navigating between different pages
            // Force scroll to top
            scrollToTop();
        }

        // Set up event listeners
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                setupNavigationListeners();
                setupSamePageListeners();
            });
        } else {
            setupNavigationListeners();
            setupSamePageListeners();
        }

        // Handle back/forward navigation
        window.addEventListener('popstate', function() {
            // When using browser back/forward, always go to top
            scrollToTop();
        });
    };

    // Start the script
    init();
})();
