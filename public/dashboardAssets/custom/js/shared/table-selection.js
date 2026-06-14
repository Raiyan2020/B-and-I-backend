/**
 * Table Selection Handler
 * Handles checkbox select all and row selection state
 */

(function() {
    'use strict';

    window.TableSelection = {
        selectedIds: {},

        _getTableId: function(tableSelector) {
            return $(tableSelector).attr('id') || String(tableSelector);
        },

        _ensureStore: function(tableId) {
            if (!this.selectedIds[tableId]) {
                this.selectedIds[tableId] = new Set();
            }

            return this.selectedIds[tableId];
        },

        /**
         * Initialize table selection
         * @param {string} tableSelector - CSS selector for the table (e.g., '#admins-table')
         */
        init: function(tableSelector) {
            var $table = $(tableSelector);
            if (!$table.length) {
                console.warn('TableSelection: Table not found:', tableSelector);
                return;
            }

            TableSelection.bindBarToTable(tableSelector);

            var $selectAllCheckbox = $table.find('thead .dt-select-all');
            if ($selectAllCheckbox.length) {
                $selectAllCheckbox.off('change.selectAll');
                $selectAllCheckbox.on('change.selectAll', function() {
                    var isChecked = $(this).is(':checked');
                    var $rowCheckboxes = $table.find('tbody .dt-select-row');

                    $rowCheckboxes.prop('checked', isChecked);
                    TableSelection.syncSelectedFromDom(tableSelector);
                    TableSelection.updateBulkActionsBar(tableSelector);
                });
            }

            $(document).off('change.tableSelection', tableSelector + ' tbody .dt-select-row');
            $(document).on('change.tableSelection', tableSelector + ' tbody .dt-select-row', function() {
                TableSelection.syncSelectedFromDom(tableSelector);
                TableSelection.updateSelectAllState(tableSelector);
                TableSelection.updateBulkActionsBar(tableSelector);
            });

            if ($.fn.DataTable && $.fn.DataTable.isDataTable(tableSelector)) {
                var dataTable = $(tableSelector).DataTable();
                dataTable.off('draw.tableSelection');
                dataTable.on('draw.tableSelection', function() {
                    TableSelection.syncSelectedFromDom(tableSelector);
                    TableSelection.updateSelectAllState(tableSelector);
                    TableSelection.updateBulkActionsBar(tableSelector);
                });
            }
        },

        /**
         * Link the bulk actions bar in the same card to this table.
         */
        bindBarToTable: function(tableSelector) {
            var tableId = this._getTableId(tableSelector);
            if (!tableId) {
                return;
            }

            var $bar = $(tableSelector)
                .closest('.card')
                .find('.bulk-actions-bar')
                .first();

            if (!$bar.length) {
                $bar = $('.bulk-actions-bar').first();
            }

            if ($bar.length) {
                $bar.attr('data-table', tableId);
            }
        },

        /**
         * Sync in-memory selection with currently rendered checkboxes.
         */
        syncSelectedFromDom: function(tableSelector) {
            var tableId = this._getTableId(tableSelector);
            var store = this._ensureStore(tableId);

            $(tableSelector).find('tbody .dt-select-row').each(function() {
                var id = String($(this).data('id'));

                if (!id || id === 'undefined') {
                    return;
                }

                if ($(this).is(':checked')) {
                    store.add(id);
                } else {
                    store.delete(id);
                }
            });
        },

        /**
         * Remove a deleted row from selection and hide the bulk bar if needed.
         */
        onRowDeleted: function(tableSelector, rowId) {
            if (!rowId) {
                return;
            }

            var tableId = this._getTableId(tableSelector);
            var store = this._ensureStore(tableId);
            store.delete(String(rowId));

            $(tableSelector).find('tbody .dt-select-row[data-id="' + rowId + '"]').prop('checked', false);
            this.updateSelectAllState(tableSelector);
            this.updateBulkActionsBar(tableSelector);
        },

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
            } else if ($selectAllCheckbox.length && $checkedRows.length === 0) {
                $selectAllCheckbox.prop('checked', false).prop('indeterminate', false);
            }
        },

        getSelectedIds: function(tableSelector) {
            this.syncSelectedFromDom(tableSelector);
            return Array.from(this._ensureStore(this._getTableId(tableSelector)));
        },

        getBulkActionsBar: function(tableSelector) {
            var tableId = this._getTableId(tableSelector);

            if (tableId) {
                var $bar = $('.bulk-actions-bar[data-table="' + tableId + '"]');
                if ($bar.length) {
                    return $bar;
                }
            }

            return $(tableSelector).closest('.card').find('.bulk-actions-bar').first();
        },

        updateBulkActionsBar: function(tableSelector) {
            var selectedIds = this.getSelectedIds(tableSelector);
            var $bulkActionsBar = this.getBulkActionsBar(tableSelector);

            if (!$bulkActionsBar.length) {
                return;
            }

            if (selectedIds.length > 0) {
                $bulkActionsBar.addClass('is-visible');
                $bulkActionsBar.find('.selected-count').text(selectedIds.length);
            } else {
                $bulkActionsBar.removeClass('is-visible');
                $bulkActionsBar.find('.selected-count').text('0');
            }
        },

        clearSelection: function(tableSelector) {
            var tableId = this._getTableId(tableSelector);
            this.selectedIds[tableId] = new Set();

            var $table = $(tableSelector);
            $table.find('thead .dt-select-all').prop('checked', false).prop('indeterminate', false);
            $table.find('tbody .dt-select-row').prop('checked', false);
            this.updateBulkActionsBar(tableSelector);
        }
    };

    $(document).ready(function() {
        $('[data-table-selection]').each(function() {
            TableSelection.init($(this).data('table-selection'));
        });
    });
})();
