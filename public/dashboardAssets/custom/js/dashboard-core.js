/**
 * Dashboard Core JavaScript
 * Contains: Theme Toggle, Scroll to Top, Sidebar State, Flash Messages
 */

(function() {
    'use strict';

    // Wait for jQuery to be loaded
    function initDashboardCore() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initDashboardCore, 100);
            return;
        }

        var $ = jQuery;

        // ============================================
        // Theme Toggle Script
        // ============================================
        (function() {
            // Get theme from localStorage or default to light
            const savedTheme = localStorage.getItem('dashboard-theme') || 'light';
            const body = document.getElementById('body-tag');
            const themeIcon = document.getElementById('theme-icon');
            const themeToggle = document.getElementById('theme-toggle');

            // Apply saved theme on page load
            function applyTheme(theme) {
                if (theme === 'dark') {
                    body.classList.add('dark-layout');
                    if (themeIcon) {
                        themeIcon.classList.remove('icon-moon');
                        themeIcon.classList.add('icon-sun');
                    }
                } else {
                    body.classList.remove('dark-layout');
                    if (themeIcon) {
                        themeIcon.classList.remove('icon-sun');
                        themeIcon.classList.add('icon-moon');
                    }
                }
            }

            // Initialize theme on page load
            applyTheme(savedTheme);

            // Handle theme toggle click
            if (themeToggle) {
                themeToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const currentTheme = body.classList.contains('dark-layout') ? 'dark' : 'light';
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                    // Save to localStorage
                    localStorage.setItem('dashboard-theme', newTheme);

                    // Apply new theme
                    applyTheme(newTheme);
                });
            }
        })();

        // ============================================
        // Scroll To Top Script
        // ============================================
        (function() {
            // Check to see if the window is top if not then display button
            $(window).on('scroll', function() {
                if ($(this).scrollTop() > 400) {
                    $('.scroll-top').fadeIn(300);
                } else {
                    $('.scroll-top').fadeOut(300);
                }
            });

            // Click event to scroll to top
            $(document).on('click', '.scroll-top', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
                return false;
            });

            // Initial check on page load
            if ($(window).scrollTop() > 400) {
                $('.scroll-top').fadeIn(300);
            }
        })();

        // ============================================
        // Sidebar Pin/Collapse Script
        // ============================================
        (function() {
            // Ensure sidebar is expanded by default
            $(window).on('load', function() {
                setTimeout(function() {
                    const body = $('body');
                    const savedState = localStorage.getItem('sidebar-state');

                    // If no saved state, default to expanded
                    if (!savedState || savedState === 'expanded') {
                        body.removeClass('menu-collapsed').addClass('menu-expanded');
                        $('.modern-nav-toggle .toggle-icon').removeClass('icon-circle').addClass('icon-disc');

                        // Update framework state if available
                        if (typeof $.app !== 'undefined' && typeof $.app.menu !== 'undefined') {
                            $.app.menu.expanded = true;
                            $.app.menu.collapsed = false;
                        }
                    } else if (savedState === 'collapsed') {
                        body.removeClass('menu-expanded').addClass('menu-collapsed');
                        $('.modern-nav-toggle .toggle-icon').removeClass('icon-disc').addClass('icon-circle');
                    }
                }, 1600);
            });

            // Save sidebar state when toggled
            $(document).on('click', '.modern-nav-toggle', function() {
                setTimeout(function() {
                    const body = $('body');
                    const isCollapsed = body.hasClass('menu-collapsed');
                    localStorage.setItem('sidebar-state', isCollapsed ? 'collapsed' : 'expanded');
                }, 300);
            });
        })();

        // ============================================
        // SweetAlert2 Flash Messages
        // ============================================
        (function() {
            // Check if SweetAlert2 is available
            if (typeof Swal === 'undefined') {
                console.warn('SweetAlert2 is not loaded. Flash messages will not work.');
                return;
            }

            // Error messages from validation
            if (typeof window.dashboardErrors !== 'undefined' && window.dashboardErrors) {
                Swal.fire({
                    position: "top-start",
                    icon: "error",
                    title: window.dashboardErrors,
                    showConfirmButton: false,
                    timer: 2500
                });
            }

            // Success flash message
            if (typeof window.dashboardSuccess !== 'undefined' && window.dashboardSuccess) {
                Swal.fire({
                    position: "top-start",
                    icon: "success",
                    title: window.dashboardSuccess,
                    showConfirmButton: false,
                    timer: 2500
                });
            }

            // Error flash message
            if (typeof window.dashboardError !== 'undefined' && window.dashboardError) {
                Swal.fire({
                    position: "top-start",
                    icon: "error",
                    title: window.dashboardError,
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        })();
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardCore);
    } else {
        initDashboardCore();
    }
})();
