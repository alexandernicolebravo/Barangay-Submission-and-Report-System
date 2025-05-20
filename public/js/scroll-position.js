/**
 * Invisible Scroll Position Preservation
 *
 * This script creates a completely invisible experience when navigating between pages
 * by eliminating any visual indication of page reload and preserving the exact scroll position.
 */

// Create a snapshot of the current page state
(function() {
    // Only run this code if we're not in an iframe
    if (window.self === window.top) {
        // Create a key for storing the scroll position
        const scrollKey = 'scrollPos_' + window.location.pathname + window.location.search;

        // Function to save scroll position
        window.saveScrollPosition = function() {
            localStorage.setItem(scrollKey, window.scrollY);
        };

        // Save scroll position on various events
        window.addEventListener('beforeunload', saveScrollPosition);

        // Create a snapshot element that will cover the entire viewport
        const snapshot = document.createElement('div');
        snapshot.id = 'page-transition-snapshot';
        snapshot.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2147483647;
            background-color: white;
            pointer-events: none;
            opacity: 0;
        `;

        // Check if we're coming from a page with the same origin
        const referrer = document.referrer;
        const sameOrigin = referrer && referrer.startsWith(window.location.origin);

        // Check if we have a saved scroll position
        const savedScrollY = localStorage.getItem(scrollKey);

        if (sameOrigin && savedScrollY !== null) {
            // We're navigating within our site and have a saved position

            // Make the snapshot visible immediately
            snapshot.style.opacity = '1';
            document.body.appendChild(snapshot);

            // Set the scroll position immediately
            window.scrollTo(0, parseInt(savedScrollY));

            // Clear the saved position
            localStorage.removeItem(scrollKey);

            // Remove the snapshot after the page has fully loaded
            window.addEventListener('load', function() {
                // Wait a tiny bit to ensure everything is rendered
                setTimeout(function() {
                    // Take a screenshot of the current viewport
                    try {
                        html2canvas(document.documentElement, {
                            x: window.scrollX,
                            y: window.scrollY,
                            width: window.innerWidth,
                            height: window.innerHeight,
                            logging: false,
                            allowTaint: true,
                            useCORS: true
                        }).then(function(canvas) {
                            // Replace the white snapshot with the screenshot
                            snapshot.style.backgroundColor = 'transparent';
                            snapshot.style.backgroundImage = `url(${canvas.toDataURL('image/jpeg', 0.95)})`;
                            snapshot.style.backgroundPosition = 'center top';
                            snapshot.style.backgroundSize = '100% auto';

                            // Fade out the snapshot very slowly
                            snapshot.style.transition = 'opacity 300ms';
                            snapshot.style.opacity = '0';

                            // Remove the snapshot after the transition
                            setTimeout(function() {
                                snapshot.remove();
                            }, 350);
                        });
                    } catch (e) {
                        // If html2canvas fails, just remove the snapshot
                        snapshot.remove();
                    }
                }, 50);
            });
        }

        // Add event listeners to all forms
        document.addEventListener('DOMContentLoaded', function() {
            // Handle form submissions
            document.querySelectorAll('form').forEach(function(form) {
                form.addEventListener('submit', saveScrollPosition);
            });

            // Handle clicks on buttons and links
            document.addEventListener('click', function(e) {
                let target = e.target;
                while (target && target !== document) {
                    if (
                        target.tagName === 'A' ||
                        target.tagName === 'BUTTON' ||
                        target.getAttribute('data-bs-toggle') === 'modal' ||
                        target.getAttribute('data-save-scroll') !== null
                    ) {
                        saveScrollPosition();
                        break;
                    }
                    target = target.parentNode;
                }
            });

            // Handle modal events
            if (typeof bootstrap !== 'undefined') {
                document.querySelectorAll('.modal').forEach(function(modal) {
                    modal.addEventListener('show.bs.modal', saveScrollPosition);
                });
            }
        });
    }
})();
