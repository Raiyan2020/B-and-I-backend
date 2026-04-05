/**
 * Table Bulk Delete Handler
 * Handles multi-delete functionality with confirmation and AJAX
 */

(function() {
    'use strict';

    window.BulkDelete = {
        /**
         * Initialize bulk delete
         * @param {object} options - Configuration options
         */
        init: function(options) {
            options = options || {};

            var defaults = {
                tableSelector: '#data-table',
                deleteUrl: null,
                csrfToken: $('meta[name="csrf-token"]').attr('content'),
                onSuccess: null,
                onError: null,
                confirmTitle: window.dashboardSwalTitle || 'هل أنت متأكد؟',
                confirmText: window.dashboardBulkDeleteText || 'سيتم حذف العناصر المحددة. لا يمكن التراجع عن هذا الإجراء!',
                confirmButtonText: window.dashboardConfirm || 'نعم، احذف',
                cancelButtonText: window.dashboardCancel || 'إلغاء',
                successTitle: window.dashboardSuccess || 'نجح',
                successText: window.dashboardItemsDeletedSuccessfully || 'تم الحذف بنجاح',
                errorTitle: window.dashboardError || 'خطأ',
                errorText: window.dashboardErrorText || 'حدث خطأ أثناء الحذف',
                selectedCountText: window.dashboardSelectedItems || 'عنصر محدد'
            };

            var config = $.extend({}, defaults, options);

            if (!config.deleteUrl) {
                console.error('BulkDelete: deleteUrl is required');
                return;
            }

            // Handle bulk delete button click
            $(document).on('click', '.bulk-delete-btn', function(e) {
                e.preventDefault();

                if (!window.TableSelection) {
                    console.error('BulkDelete: TableSelection is required');
                    return;
                }

                var selectedIds = window.TableSelection.getSelectedIds(config.tableSelector);

                if (selectedIds.length === 0) {
                    Swal.fire({
                        position: 'top-start',
                        icon: 'warning',
                        title: window.dashboardNoSelection || 'لا يوجد اختيار',
                        text: window.dashboardPleaseSelectItems || 'يرجى تحديد عناصر للحذف',
                        showConfirmButton: false,
                        timer: 2000,
                    });
                    return;
                }

                var countText = selectedIds.length + ' ' + config.selectedCountText;

                Swal.fire({
                    title: config.confirmTitle,
                    html: '<p>' + config.confirmText + '</p><p><strong>' + countText + '</strong></p>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: config.confirmButtonText,
                    cancelButtonText: config.cancelButtonText,
                    confirmButtonClass: 'btn btn-primary',
                    cancelButtonClass: 'btn btn-danger ml-1',
                }).then((result) => {
                    if (result.isConfirmed) {
                        BulkDelete.performDelete(config, selectedIds);
                    }
                });
            });

            // Handle clear selection button
            $(document).on('click', '.bulk-clear-selection', function(e) {
                e.preventDefault();
                if (window.TableSelection) {
                    window.TableSelection.clearSelection(config.tableSelector);
                }
            });
        },

        /**
         * Perform bulk delete
         */
        performDelete: function(config, selectedIds) {
            // Show loading
            Swal.fire({
                title: window.dashboardDeleting || 'جاري الحذف...',
                text: window.dashboardPleaseWait || 'يرجى الانتظار',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                type: 'POST',
                url: config.deleteUrl,
                data: {
                    _token: config.csrfToken,
                    data: JSON.stringify(selectedIds.map(function(id) {
                        return { id: id };
                    }))
                },
                dataType: 'json',
                success: function(response) {
                    Swal.close();

                    var icon = (response.key === 'success') ? 'success' : 'warning';
                    var title = (response.key === 'success') ? config.successTitle : config.errorTitle;
                    var message = response.msg || ((response.key === 'success') ? config.successText : config.errorText);

                    Swal.fire({
                        position: 'top-start',
                        icon: icon,
                        title: title,
                        text: message,
                        showConfirmButton: false,
                        timer: 2000,
                    });

                    // Clear selection
                    if (window.TableSelection) {
                        window.TableSelection.clearSelection(config.tableSelector);
                    }

                    // Reload table if DataTables
                    var $table = $(config.tableSelector);
                    if ($.fn.DataTable && $.fn.DataTable.isDataTable(config.tableSelector)) {
                        $table.DataTable().ajax.reload(null, false);
                    } else {
                        // For non-DataTables, reload page or trigger custom reload
                        if (config.onSuccess && typeof config.onSuccess === 'function') {
                            config.onSuccess(response);
                        } else {
                            location.reload();
                        }
                    }
                },
                error: function(xhr) {
                    Swal.close();

                    var message = config.errorText;
                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                        message = xhr.responseJSON.msg;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        position: 'top-start',
                        icon: 'error',
                        title: config.errorTitle,
                        text: message,
                        showConfirmButton: false,
                        timer: 3000,
                    });

                    if (config.onError && typeof config.onError === 'function') {
                        config.onError(xhr);
                    }
                }
            });
        }
    };
})();
