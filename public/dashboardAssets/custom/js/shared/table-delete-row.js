/**
 * Table Delete Row Handler
 * Improved version of deleteOne.blade.php with fixed context issues
 */

(function() {
    'use strict';

    // Wait for jQuery and SweetAlert2
    function initDeleteRow() {
        if (typeof jQuery === 'undefined' || typeof Swal === 'undefined') {
            setTimeout(initDeleteRow, 100);
            return;
        }

        var $ = jQuery;

        $(document).on('click', '.delete-row', function(e) {
            e.preventDefault();
            
            // Store reference to clicked element
            var $deleteBtn = $(this);
            var deleteUrl = $deleteBtn.data('url');
            
            if (!deleteUrl) {
                console.warn('Delete row: URL not found');
                return;
            }

            // Get translations (fallback to defaults)
            var title = window.dashboardSwalTitle || 'هل أنت متأكد؟';
            var text = window.dashboardSwalText || 'لا يمكن التراجع عن هذا الإجراء!';
            var confirmText = window.dashboardConfirm || 'نعم، احذف';
            var cancelText = window.dashboardCancel || 'إلغاء';
            var successMsg = window.dashboardItemDeletedSuccessfully || 'تم الحذف بنجاح';

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: confirmText,
                confirmButtonClass: 'btn btn-primary',
                cancelButtonText: cancelText,
                cancelButtonClass: 'btn btn-danger ml-1',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: deleteUrl,
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function(response) {
                            var icon = response.error ? 'error' : 'success';
                            var title = response.error || 'success';
                            var message = response.msg || response.key || successMsg;

                            Swal.fire({
                                position: 'top-start',
                                icon: icon,
                                title: title,
                                text: message,
                                showConfirmButton: false,
                                timer: 1500,
                                confirmButtonClass: 'btn btn-primary',
                            });

                            // Remove row if successful
                            if (!response.error && response.key !== 'error') {
                                $deleteBtn.closest('tr').fadeOut(300, function() {
                                    $(this).remove();
                                    
                                    // Reload DataTable if exists
                                    var $table = $deleteBtn.closest('table');
                                    if ($.fn.DataTable && $.fn.DataTable.isDataTable($table)) {
                                        $table.DataTable().draw(false);
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            var message = 'حدث خطأ أثناء الحذف';
                            if (xhr.responseJSON && xhr.responseJSON.msg) {
                                message = xhr.responseJSON.msg;
                            }
                            
                            Swal.fire({
                                position: 'top-start',
                                icon: 'error',
                                title: 'خطأ',
                                text: message,
                                showConfirmButton: false,
                                timer: 2000,
                            });
                        }
                    });
                }
            });
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDeleteRow);
    } else {
        initDeleteRow();
    }
})();
