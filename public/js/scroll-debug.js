/**
 * Scroll Position Debug
 *
 * This script adds a debug panel to help troubleshoot scroll position issues.
 * It's designed to work with the seamless scroll position preservation script.
 */

// Wait for the page to be fully loaded
window.addEventListener('load', function() {
    // Create debug panel
    const debugPanel = document.createElement('div');
    debugPanel.style.position = 'fixed';
    debugPanel.style.bottom = '10px';
    debugPanel.style.right = '10px';
    debugPanel.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
    debugPanel.style.color = 'white';
    debugPanel.style.padding = '10px';
    debugPanel.style.borderRadius = '5px';
    debugPanel.style.zIndex = '9999';
    debugPanel.style.fontSize = '12px';
    debugPanel.style.maxWidth = '300px';
    debugPanel.style.maxHeight = '200px';
    debugPanel.style.overflow = 'auto';

    // Add toggle button
    const toggleButton = document.createElement('button');
    toggleButton.textContent = 'Scroll Debug';
    toggleButton.style.position = 'fixed';
    toggleButton.style.bottom = '10px';
    toggleButton.style.right = '10px';
    toggleButton.style.zIndex = '10000';
    toggleButton.style.padding = '5px 10px';
    toggleButton.style.backgroundColor = '#4e73df';
    toggleButton.style.color = 'white';
    toggleButton.style.border = 'none';
    toggleButton.style.borderRadius = '3px';
    toggleButton.style.cursor = 'pointer';
    toggleButton.style.fontSize = '11px';
    toggleButton.style.fontWeight = 'bold';

    // Initially hide the debug panel
    debugPanel.style.display = 'none';

    // Toggle debug panel visibility
    toggleButton.addEventListener('click', function() {
        if (debugPanel.style.display === 'none') {
            debugPanel.style.display = 'block';
            updateDebugInfo();
        } else {
            debugPanel.style.display = 'none';
        }
    });

    // Function to update debug information
    function updateDebugInfo() {
        if (debugPanel.style.display === 'none') return;

        // Clear previous content
        debugPanel.innerHTML = '';

        // Add current scroll position
        const scrollInfo = document.createElement('div');
        scrollInfo.innerHTML = `<strong>Current Scroll:</strong> ${Math.round(window.scrollY)}px`;
        debugPanel.appendChild(scrollInfo);

        // Add page info
        const pageInfo = document.createElement('div');
        pageInfo.innerHTML = `<strong>Page:</strong> ${window.location.pathname}${window.location.search}`;
        debugPanel.appendChild(pageInfo);

        // Add key info
        const keyInfo = document.createElement('div');
        keyInfo.innerHTML = `<strong>Storage Key:</strong> scrollPosition_${window.location.pathname}${window.location.search}`;
        debugPanel.appendChild(keyInfo);

        // Add localStorage info
        const storageInfo = document.createElement('div');
        storageInfo.innerHTML = '<strong>Saved Positions:</strong>';
        debugPanel.appendChild(storageInfo);

        // List all scroll position entries
        const scrollPositionEntries = document.createElement('ul');
        scrollPositionEntries.style.paddingLeft = '20px';
        scrollPositionEntries.style.margin = '5px 0';

        let hasEntries = false;
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key && key.startsWith('scrollPosition_') && !key.endsWith('_timestamp')) {
                hasEntries = true;
                const value = localStorage.getItem(key);
                const timestamp = localStorage.getItem(key + '_timestamp');
                let timeAgo = '';

                if (timestamp) {
                    const age = Math.round((Date.now() - parseInt(timestamp)) / 1000);
                    timeAgo = age < 60 ? `${age}s ago` : `${Math.round(age/60)}m ago`;
                }

                const entry = document.createElement('li');
                const path = key.replace('scrollPosition_', '');
                entry.innerHTML = `<span style="color:#aaddff;">${path || '/'}</span>: <b>${value}px</b> ${timeAgo ? `<span style="color:#aaaaaa;">(${timeAgo})</span>` : ''}`;
                scrollPositionEntries.appendChild(entry);
            }
        }

        if (!hasEntries) {
            const noEntries = document.createElement('div');
            noEntries.style.color = '#aaaaaa';
            noEntries.style.fontStyle = 'italic';
            noEntries.style.marginTop = '5px';
            noEntries.textContent = 'No saved positions found';
            storageInfo.appendChild(noEntries);
        } else {
            storageInfo.appendChild(scrollPositionEntries);
        }

        // Add action buttons
        const buttonContainer = document.createElement('div');
        buttonContainer.style.display = 'flex';
        buttonContainer.style.gap = '5px';
        buttonContainer.style.marginTop = '10px';

        // Add save button
        const saveButton = document.createElement('button');
        saveButton.textContent = 'Save Position';
        saveButton.style.padding = '3px 8px';
        saveButton.style.backgroundColor = '#1cc88a';
        saveButton.style.color = 'white';
        saveButton.style.border = 'none';
        saveButton.style.borderRadius = '3px';
        saveButton.style.cursor = 'pointer';
        saveButton.style.fontSize = '11px';

        saveButton.addEventListener('click', function() {
            if (typeof saveScrollPosition === 'function') {
                saveScrollPosition();
                updateDebugInfo();
            }
        });

        // Add clear button
        const clearButton = document.createElement('button');
        clearButton.textContent = 'Clear All';
        clearButton.style.padding = '3px 8px';
        clearButton.style.backgroundColor = '#e74a3b';
        clearButton.style.color = 'white';
        clearButton.style.border = 'none';
        clearButton.style.borderRadius = '3px';
        clearButton.style.cursor = 'pointer';
        clearButton.style.fontSize = '11px';

        clearButton.addEventListener('click', function() {
            // Clear all scroll position entries
            const keysToRemove = [];
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key && key.startsWith('scrollPosition_')) {
                    keysToRemove.push(key);
                }
            }

            keysToRemove.forEach(key => {
                localStorage.removeItem(key);
                if (!key.endsWith('_timestamp')) {
                    localStorage.removeItem(key + '_timestamp');
                }
            });

            updateDebugInfo();
        });

        buttonContainer.appendChild(saveButton);
        buttonContainer.appendChild(clearButton);
        debugPanel.appendChild(buttonContainer);
    }

    // Update debug info every second
    setInterval(updateDebugInfo, 1000);

    // Add elements to the document
    document.body.appendChild(debugPanel);
    document.body.appendChild(toggleButton);

    // Initial update
    updateDebugInfo();
});
