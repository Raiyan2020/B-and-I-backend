/**
 * DataTables Global Initializer
 * Sets default language and processing indicator for all DataTables
 */

(function() {
    'use strict';

    function getLanguageConfig() {
        if (window.dashboardDataTablesLanguage) {
            return window.dashboardDataTablesLanguage;
        }

        return {
            search: window.dashboardDataTablesSearch || 'Search:',
            processing: "<span class='fa-stack fa-lg'><i class='fa fa-spinner fa-spin fa-stack-2x fa-fw'></i></span>",
            lengthMenu: window.dashboardDataTablesLengthMenu || 'Show _MENU_ entries',
            info: window.dashboardDataTablesInfo || 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: window.dashboardDataTablesInfoEmpty || 'Showing 0 to 0 of 0 entries',
            infoFiltered: window.dashboardDataTablesInfoFiltered || '(filtered from _MAX_ total entries)',
            emptyTable: window.dashboardDataTablesEmptyTable || 'No data available in table',
            zeroRecords: window.dashboardDataTablesZeroRecords || 'No matching records found',
            paginate: {
                first: window.dashboardDataTablesFirst || 'First',
                last: window.dashboardDataTablesLast || 'Last',
                next: window.dashboardDataTablesNext || 'Next',
                previous: window.dashboardDataTablesPrevious || 'Previous',
            },
            aria: {
                sortAscending: window.dashboardDataTablesSortAscending || ': activate to sort column ascending',
                sortDescending: window.dashboardDataTablesSortDescending || ': activate to sort column descending',
            },
        };
    }

    function applyDataTablesDefaults() {
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.dataTable === 'undefined') {
            return false;
        }

        jQuery.extend(true, jQuery.fn.dataTable.defaults, {
            language: getLanguageConfig(),
        });

        return true;
    }

    function initDataTables() {
        if (applyDataTablesDefaults()) {
            return;
        }

        setTimeout(initDataTables, 100);
    }

    window.dashboardApplyDataTablesDefaults = applyDataTablesDefaults;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDataTables);
    } else {
        initDataTables();
    }
})();
