/**
 * DataTables Global Initializer
 * Sets default language and processing indicator for all DataTables
 */

(function() {
    'use strict';

    // Wait for jQuery and DataTables
    function initDataTables() {
        if (typeof jQuery === 'undefined' || typeof $.fn.dataTable === 'undefined') {
            setTimeout(initDataTables, 100);
            return;
        }

        var $ = jQuery;

        // Extend DataTables defaults
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                search: window.dashboardDataTablesSearch || "Search",
                "processing": "<span class='fa-stack fa-lg'>\n\
                            <i class='fa fa-spinner fa-spin fa-stack-2x fa-fw'></i>\n\
                       </span>",
                "lengthMenu": window.dashboardDataTablesLengthMenu || "Show _MENU_ entries",
                "info": window.dashboardDataTablesInfo || "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": window.dashboardDataTablesInfoEmpty || "Showing 0 to 0 of 0 entries",
                "infoFiltered": window.dashboardDataTablesInfoFiltered || "(filtered from _MAX_ total entries)",
                "emptyTable": window.dashboardDataTablesEmptyTable || "No data available in table",
                "zeroRecords": window.dashboardDataTablesZeroRecords || "No matching records found",
                "paginate": {
                    "first": window.dashboardDataTablesFirst || "First",
                    "last": window.dashboardDataTablesLast || "Last",
                    "next": window.dashboardDataTablesNext || "Next",
                    "previous": window.dashboardDataTablesPrevious || "Previous"
                },
                "aria": {
                    "sortAscending": window.dashboardDataTablesSortAscending || ": activate to sort column ascending",
                    "sortDescending": window.dashboardDataTablesSortDescending || ": activate to sort column descending"
                }
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDataTables);
    } else {
        initDataTables();
    }
})();
