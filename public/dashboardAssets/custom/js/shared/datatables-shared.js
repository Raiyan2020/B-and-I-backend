/**
 * DataTables Shared Configuration
 * Common initialization and helper functions for DataTables
 */

(function() {
    'use strict';

    if (typeof jQuery === 'undefined' || typeof $.fn.dataTable === 'undefined') {
        console.warn('DataTables Shared: jQuery or DataTables not loaded');
        return;
    }

    var $ = jQuery;

    window.DataTablesShared = {
        /**
         * Default DataTables configuration
         */
        defaults: {
            processing: true,
            serverSide: true,
            lengthMenu: [10, 20, 40, 60, 80, 100],
            pageLength: 10,
            language: window.dashboardDataTablesLanguage || {
                search: window.dashboardDataTablesSearch || 'Search:',
                processing: "<span class='fa-stack fa-lg'><i class='fa fa-spinner fa-spin fa-stack-2x fa-fw'></i></span>",
                lengthMenu: window.dashboardDataTablesLengthMenu || "Show _MENU_ entries",
                info: window.dashboardDataTablesInfo || "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: window.dashboardDataTablesInfoEmpty || "Showing 0 to 0 of 0 entries",
                infoFiltered: window.dashboardDataTablesInfoFiltered || "(filtered from _MAX_ total entries)",
                emptyTable: window.dashboardDataTablesEmptyTable || "No data available in table",
                zeroRecords: window.dashboardDataTablesZeroRecords || "No matching records found",
                paginate: {
                    first: window.dashboardDataTablesFirst || "First",
                    last: window.dashboardDataTablesLast || "Last",
                    next: window.dashboardDataTablesNext || "Next",
                    previous: window.dashboardDataTablesPrevious || "Previous"
                },
                aria: {
                    sortAscending: window.dashboardDataTablesSortAscending || ": activate to sort column ascending",
                    sortDescending: window.dashboardDataTablesSortDescending || ": activate to sort column descending"
                }
            },
            order: [[0, 'desc']],
            autoWidth: false,
            responsive: true,
        },

        /**
         * Render image column helper
         */
        renderImage: function(defaultIcon, alt) {
            defaultIcon = defaultIcon || 'feather icon-image';
            alt = alt || 'Image';
            
            return function(data, type, row) {
                if (type === 'display') {
                    if (window.TableHelpers) {
                        return window.TableHelpers.renderImage(data, defaultIcon, alt);
                    }
                    
                    // Fallback if TableHelpers not loaded
                    if (data && data !== null && data !== '' && data !== undefined) {
                        return `<img class="table-image" src="${data}" alt="${alt}" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'table-image-placeholder\\'><i class=\\'${defaultIcon}\\'></i></div>';" />`;
                    }
                    return `<div class="table-image-placeholder"><i class="${defaultIcon}"></i></div>`;
                }
                return data;
            };
        },

        /**
         * Render actions column helper
         */
        renderActions: function(options) {
            options = options || {};
            
            return function(data, type, row) {
                if (type === 'display') {
                    var editRoute = options.editRoute ? options.editRoute.replace(':id', data) : null;
                    var deleteRoute = options.deleteRoute ? options.deleteRoute.replace(':id', data) : null;
                    var showRoute = options.showRoute ? options.showRoute.replace(':id', data) : null;
                    var statusRoute = options.statusRoute ? options.statusRoute.replace(':id', data) : null;
                    var hideIf = options.hideIf ? options.hideIf(data, row) : false;

                    if (hideIf) {
                        return '';
                    }

                    var html = '<div class="d-flex align-items-center gap-2">';
                    
                    if (editRoute) {
                        html += `<a class="btn btn-sm btn-icon btn-outline-primary" href="${editRoute}" title="Edit"><i class="feather icon-edit text-primary"></i></a>`;
                    }
                    
                    if (statusRoute) {
                        html += `<a class="btn btn-sm btn-icon btn-outline-warning" href="${statusRoute}" title="Change Status"><i class="feather icon-slash text-warning"></i></a>`;
                    }
                    
                    if (showRoute) {
                        html += `<a class="btn btn-sm btn-icon btn-outline-info" href="${showRoute}" title="Show"><i class="feather icon-eye text-info"></i></a>`;
                    }
                    
                    if (deleteRoute) {
                        html += `<button type="button" class="btn btn-sm btn-icon btn-outline-danger delete-row" data-url="${deleteRoute}" title="Delete"><i class="feather icon-trash-2 text-danger"></i></button>`;
                    }
                    
                    html += '</div>';
                    return html;
                }
                return data;
            };
        },

        /**
         * Render badge helper
         */
        renderBadge: function(options) {
            options = options || {
                active: { value: 1, text: window.dashboardActive || 'Active', class: 'success' },
                inactive: { value: 0, text: window.dashboardInActive || 'Inactive', class: 'danger' }
            };

            return function(data, type, row) {
                if (type === 'display') {
                    if (window.TableHelpers) {
                        return window.TableHelpers.renderBadge(data, options);
                    }
                    
                    // Fallback
                    var isActive = (data == options.active.value || data === true);
                    var config = isActive ? options.active : options.inactive;
                    return `<span class="badge badge-${config.class}">${config.text}</span>`;
                }
                return data;
            };
        },

        /**
         * Sync DataTables sort direction with the order filter dropdown.
         */
        syncOrderFilter: function(table, columnIndex, filterSelector) {
            if (!table) {
                return;
            }

            filterSelector = filterSelector || '#order-filter';
            var dir = ($(filterSelector).val() || 'ASC').toLowerCase();
            table.order([columnIndex, dir]).draw(false);
        },

        /**
         * Initialize DataTable with common options
         */
        init: function(tableSelector, customOptions) {
            var tableId = $(tableSelector).attr('id');
            if (!tableId) {
                console.error('DataTables Shared: Table must have an ID');
                return null;
            }

            var options = $.extend(true, {}, this.defaults, customOptions);
            
            // Add checkbox column if selection is enabled
            if (options.selection !== false) {
                // Checkbox column will be added via Blade component
                // Just ensure it's included in the initialization
            }

            var table = $(tableSelector).DataTable(options);

            // Initialize table selection if checkboxes exist
            if (window.TableSelection && $(tableSelector).find('.dt-select-all').length) {
                window.TableSelection.init(tableSelector);
            }

            return table;
        }
    };
})();
