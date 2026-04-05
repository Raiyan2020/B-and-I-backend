/**
 * SweetAlert2 Flash Messages Handler
 * Handles Laravel flash messages via SweetAlert2
 */

(function() {
    'use strict';

    // Wait for SweetAlert2
    function initSwalFlash() {
        if (typeof Swal === 'undefined') {
            setTimeout(initSwalFlash, 100);
            return;
        }

        // Error messages from validation (Laravel $errors)
        if (typeof window.dashboardErrors !== 'undefined' && window.dashboardErrors && window.dashboardErrors !== '') {
            Swal.fire({
                position: "top-start",
                icon: "error",
                title: window.dashboardErrors,
                showConfirmButton: false,
                timer: 2500
            });
        }

        // Success flash message
        if (typeof window.dashboardSuccess !== 'undefined' && window.dashboardSuccess && window.dashboardSuccess !== '') {
            Swal.fire({
                position: "top-start",
                icon: "success",
                title: window.dashboardSuccess,
                showConfirmButton: false,
                timer: 2500
            });
        }

        // Error flash message
        if (typeof window.dashboardError !== 'undefined' && window.dashboardError && window.dashboardError !== '') {
            Swal.fire({
                position: "top-start",
                icon: "error",
                title: window.dashboardError,
                showConfirmButton: false,
                timer: 2500
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSwalFlash);
    } else {
        initSwalFlash();
    }
})();
