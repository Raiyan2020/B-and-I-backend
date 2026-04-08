/**
 * Sync chevron + aria-expanded with Bootstrap 4 collapse for .admin-collapsible-panel
 */
(function () {
    'use strict';

    function init($) {
        $(document).on('shown.bs.collapse', '.admin-collapsible-panel .collapse', function () {
            var $wrap = $(this).closest('.admin-collapsible-panel');
            $wrap.find('.admin-collapsible-chevron').addClass('is-open');
            $wrap.find('.admin-collapsible-panel-toggle').attr('aria-expanded', 'true');
        });
        $(document).on('hidden.bs.collapse', '.admin-collapsible-panel .collapse', function () {
            var $wrap = $(this).closest('.admin-collapsible-panel');
            $wrap.find('.admin-collapsible-chevron').removeClass('is-open');
            $wrap.find('.admin-collapsible-panel-toggle').attr('aria-expanded', 'false');
        });
    }

    if (typeof jQuery !== 'undefined') {
        jQuery(init);
    }
})();
