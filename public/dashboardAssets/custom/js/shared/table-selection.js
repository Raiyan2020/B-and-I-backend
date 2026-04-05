/**
 * Table Selection Handler
 * Handles checkbox select all and row selection state
 */

(function() {
    'use strict';

    window.TableSelection = {
        /**
         * Initialize table selection
         * @param {string} tableSelector - CSS selector for the table (e.g., '#admins-table')
         * @param {object} options - Configuration options
         */
        init: function(tableSelector, options) {
            options = options || {};

            var $table = $(tableSelector);
            if (!$table.length) {
                console.warn('TableSelection: Table not found:', tableSelector);
                return;
            }

            // Select All functionality
            var $selectAllCheckbox = $table.find('thead .dt-select-all');
            if ($selectAllCheckbox.length) {
                // Remove any existing handlers to avoid duplicates
                $selectAllCheckbox.off('change.selectAll');

                // Bind change event
                $selectAllCheckbox.on('change.selectAll', function() {
                    var isChecked = $(this).is(':checked');
                    // Get fresh checkboxes on each click (important for server-side DataTables)
                    var $rowCheckboxes = $table.find('tbody .dt-select-row');

                    $rowCheckboxes.prop('checked', isChecked);
                    TableSelection.updateBulkActionsBar(tableSelector);
                });
            }

            // Row checkbox change
            $(document).on('change', tableSelector + ' tbody .dt-select-row', function() {
                TableSelection.updateSelectAllState(tableSelector);
                TableSelection.updateBulkActionsBar(tableSelector);
            });

            // Update state on DataTables draw (for server-side tables)
            if ($.fn.DataTable && $.fn.DataTable.isDataTable(tableSelector)) {
                var dataTable = $(tableSelector).DataTable();
                dataTable.on('draw.dt', function() {
                    TableSelection.updateSelectAllState(tableSelector);
                    TableSelection.updateBulkActionsBar(tableSelector);
                });
            }
        },

        /**
         * Update select all checkbox state based on row checkboxes
         */
        updateSelectAllState: function(tableSelector) {
            var $table = $(tableSelector);
            var $selectAllCheckbox = $table.find('thead .dt-select-all');
            var $rowCheckboxes = $table.find('tbody .dt-select-row:visible');
            var $checkedRows = $rowCheckboxes.filter(':checked');

            if ($selectAllCheckbox.length && $rowCheckboxes.length > 0) {
                if ($checkedRows.length === 0) {
                    $selectAllCheckbox.prop('checked', false).prop('indeterminate', false);
                } else if ($checkedRows.length === $rowCheckboxes.length) {
                    $selectAllCheckbox.prop('checked', true).prop('indeterminate', false);
                } else {
                    $selectAllCheckbox.prop('checked', false).prop('indeterminate', true);
                }
            }
        },

        /**
         * Get selected row IDs
         * @returns {Array} Array of selected IDs
         */
        getSelectedIds: function(tableSelector) {
            var $table = $(tableSelector);
            var selectedIds = [];

            $table.find('tbody .dt-select-row:checked').each(function() {
                var id = $(this).data('id');
                if (id) {
                    selectedIds.push(id);
                }
            });

            return selectedIds;
        },

        /**
         * Update bulk actions bar visibility
         */
        updateBulkActionsBar: function(tableSelector) {
            var selectedIds = this.getSelectedIds(tableSelector);
            var $bulkActionsBar = $('.bulk-actions-bar');

            if (selectedIds.length > 0) {
                $bulkActionsBar.fadeIn(200);
                $bulkActionsBar.find('.selected-count').text(selectedIds.length);
            } else {
                $bulkActionsBar.fadeOut(200);
            }
        },

        /**
         * Clear all selections
         */
        clearSelection: function(tableSelector) {
            var $table = $(tableSelector);
            var $selectAllCheckbox = $table.find('thead .dt-select-all');
            var $rowCheckboxes = $table.find('tbody .dt-select-row');

            $selectAllCheckbox.prop('checked', false).prop('indeterminate', false);
            $rowCheckboxes.prop('checked', false);
            this.updateBulkActionsBar(tableSelector);
        }
    };

    // Auto-initialize on DOM ready if data attribute is present
    $(document).ready(function() {
        $('[data-table-selection]').each(function() {
            var tableSelector = $(this).data('table-selection');
            TableSelection.init(tableSelector);
        });
    });
})();
