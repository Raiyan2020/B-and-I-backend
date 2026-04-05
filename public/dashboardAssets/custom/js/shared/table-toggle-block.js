/**
 * Table Toggle Block Handler
 * Handles toggle block functionality with AJAX and SweetAlert
 */

(function() {
    'use strict';

    window.ToggleBlock = {
        /**
         * Initialize toggle block functionality
         * @param {object} options - Configuration options
         */
        init: function(options) {
            options = options || {};

            var defaults = {
                tableSelector: '#data-table',
                csrfToken: $('meta[name="csrf-token"]').attr('content'),
                confirmTitle: window.dashboardSwalTitle || 'هل أنت متأكد؟',
                confirmText: window.dashboardToggleBlockText || 'هل تريد تغيير حالة الحظر لهذا العنصر؟',
                confirmButtonText: window.dashboardConfirm || 'نعم',
                cancelButtonText: window.dashboardCancel || 'إلغاء',
                successTitle: window.dashboardSuccess || 'نجح',
                errorTitle: window.dashboardError || 'خطأ',
                errorText: window.dashboardErrorText || 'حدث خطأ أثناء العملية'
            };

            var config = $.extend({}, defaults, options);

            // Handle toggle block button click
            $(document).on('click', '.toggle-block-btn', function(e) {
                e.preventDefault();

                var $btn = $(this);
                var toggleUrl = $btn.data('url');
                var isBlocked = $btn.data('blocked') == 1;
                var blockText = isBlocked ? window.dashboardUnBlock || 'إلغاء الحظر' : window.dashboardBlock || 'حظر';

                if (!toggleUrl) {
                    console.error('ToggleBlock: URL is required');
                    return;
                }

                Swal.fire({
                    title: config.confirmTitle,
                    text: config.confirmText + ' (' + blockText + ')',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: config.confirmButtonText,
                    cancelButtonText: config.cancelButtonText,
                    confirmButtonClass: 'btn btn-primary',
                    cancelButtonClass: 'btn btn-danger ml-1',
                }).then((result) => {
                    if (result.isConfirmed) {
                        ToggleBlock.performToggle(config, toggleUrl);
                    }
                });
            });
        },

        /**
         * Perform toggle block via AJAX
         */
        performToggle: function(config, toggleUrl) {
            // Show loading
            Swal.fire({
                title: window.dashboardLoading || 'جاري المعالجة...',
                text: window.dashboardPleaseWait || 'يرجى الانتظار',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                type: 'GET',
                url: toggleUrl,
                headers: {
                    'X-CSRF-TOKEN': config.csrfToken
                },
                dataType: 'json',
                success: function(response) {
                    Swal.close();

                    var icon = 'success';
                    var title = config.successTitle;
                    var message = response.msg || response.message || (window.dashboardItemUpdatedSuccessfully || 'تم التحديث بنجاح');

                    Swal.fire({
                        position: 'top-start',
                        icon: icon,
                        title: title,
                        text: message,
                        showConfirmButton: false,
                        timer: 2000,
                    });

                    // Reload DataTable if exists
                    var $table = $(config.tableSelector);
                    if ($.fn.DataTable && $.fn.DataTable.isDataTable(config.tableSelector)) {
                        $table.DataTable().ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    Swal.close();

                    var message = config.errorText;
                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                        message = xhr.responseJSON.msg;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            var errorResponse = JSON.parse(xhr.responseText);
                            message = errorResponse.msg || errorResponse.message || message;
                        } catch (e) {
                            message = xhr.responseText;
                        }
                    }

                    Swal.fire({
                        position: 'top-start',
                        icon: 'error',
                        title: config.errorTitle,
                        text: message,
                        showConfirmButton: false,
                        timer: 3000,
                    });
                }
            });
        }
    };
})();
