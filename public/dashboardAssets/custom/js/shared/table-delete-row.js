/**
 * Table Delete Row Handler
 * Improved version of deleteOne.blade.php with fixed context issues
 */

(function() {
    'use strict';

    function getSuccessTitle() {
        return window.dashboardSuccessTitle || 'نجح';
    }

    function getErrorTitle() {
        return window.dashboardErrorTitle || 'خطأ';
    }

    function initDeleteRow() {
        if (typeof jQuery === 'undefined' || typeof Swal === 'undefined') {
            setTimeout(initDeleteRow, 100);
            return;
        }

        var $ = jQuery;

        $(document).on('click', '.delete-row', function(e) {
            e.preventDefault();

            var $deleteBtn = $(this);
            var deleteUrl = $deleteBtn.data('url');

            if (!deleteUrl) {
                console.warn('Delete row: URL not found');
                return;
            }

            var $row = $deleteBtn.closest('tr');
            var $table = $deleteBtn.closest('table');
            var tableSelector = $table.attr('id') ? '#' + $table.attr('id') : null;
            var deletedId = $row.find('.dt-select-row').data('id');

            Swal.fire({
                title: window.dashboardSwalTitle || 'هل أنت متأكد؟',
                text: window.dashboardSwalText || 'لا يمكن التراجع عن هذا الإجراء!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: window.dashboardConfirm || 'نعم، احذف',
                confirmButtonClass: 'btn btn-primary',
                cancelButtonText: window.dashboardCancel || 'إلغاء',
                cancelButtonClass: 'btn btn-danger ml-1',
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    type: 'DELETE',
                    url: deleteUrl,
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(response) {
                        var isError = !!(response.error || response.key === 'error');
                        var message = response.msg || window.dashboardItemDeletedSuccessfully || 'تم الحذف بنجاح';

                        Swal.fire({
                            position: 'top-start',
                            icon: isError ? 'error' : 'success',
                            title: isError ? getErrorTitle() : getSuccessTitle(),
                            text: message,
                            showConfirmButton: false,
                            timer: 1500,
                        });

                        if (isError) {
                            return;
                        }

                        if (window.TableSelection && tableSelector) {
                            window.TableSelection.onRowDeleted(tableSelector, deletedId);
                        }

                        if ($.fn.DataTable && $.fn.DataTable.isDataTable($table)) {
                            $table.DataTable().ajax.reload(null, false);
                            return;
                        }

                        $row.remove();
                    },
                    error: function(xhr) {
                        var message = window.dashboardGenericError || 'حدث خطأ أثناء الحذف';

                        if (xhr.responseJSON && xhr.responseJSON.msg) {
                            message = xhr.responseJSON.msg;
                        }

                        Swal.fire({
                            position: 'top-start',
                            icon: 'error',
                            title: getErrorTitle(),
                            text: message,
                            showConfirmButton: false,
                            timer: 2000,
                        });
                    }
                });
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDeleteRow);
    } else {
        initDeleteRow();
    }
})();
