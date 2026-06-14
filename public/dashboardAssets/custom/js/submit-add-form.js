/**
 * Submit Add Form Handler
 * Handles AJAX form submissions for .store forms
 */

(function() {
    'use strict';

    function collectValidationMessages(errors) {
        var lines = [];
        if (!errors || typeof errors !== 'object') {
            return lines;
        }
        $.each(errors, function(key, value) {
            if ($.isArray(value)) {
                $.each(value, function(i, msg) {
                    if (msg) {
                        lines.push(msg);
                    }
                });
            } else if (typeof value === 'string') {
                lines.push(value);
            }
        });
        return lines;
    }

    function showValidationToast(lines) {
        if (!lines.length) {
            return;
        }
        var body = lines.join('<br>');
        var title = window.dashboardValidationTitle || 'Validation';
        if (typeof toastr !== 'undefined') {
            toastr.error(body, title, {
                timeOut: 12000,
                enableHtml: true
            });
        }
    }

    // Wait for jQuery and SweetAlert2
    function initSubmitForm() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initSubmitForm, 100);
            return;
        }

        var $ = jQuery;

        $(document).ready(function() {
            $(document).on('submit', '.store', function(e) {
                e.preventDefault();
                var url = $(this).attr('action');
                var form = $(this);

                if (typeof CKEDITOR !== 'undefined') {
                    for (var key in CKEDITOR.instances) {
                        if (CKEDITOR.instances.hasOwnProperty(key)) {
                            CKEDITOR.instances[key].updateElement();
                        }
                    }
                }

                $.ajax({
                    url: url,
                    method: 'post',
                    data: new FormData(this),
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $(".submit_button").html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                        ).attr('disabled', true);
                    },
                    success: function(response) {
                        $(".text-danger").remove();
                        $('.store input').removeClass('border-danger');
                        $(".submit_button").html(window.dashboardSendText || "Send").attr('disabled', false);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                position: "top-start",
                                icon: "success",
                                title: response.msg || (window.dashboardAddedSuccessfully || 'Added successfully'),
                                showConfirmButton: false,
                                buttonsStyling: false,
                                timer: 1500
                            });
                        }

                        if (response.url) {
                            setTimeout(function() {
                                window.location.replace(response.url);
                            }, 1000);
                        }

                        if (response.redirect === false) {
                            return;
                        } else {
                            if (typeof window.resetDashboardForm === 'function') {
                                window.resetDashboardForm(form);
                            } else {
                                form.trigger('reset');
                            }
                            var table = form.closest('.card').next().find('table');
                            if ($.fn.DataTable && $.fn.DataTable.isDataTable(table)) {
                                table.DataTable().ajax.reload(null, false);
                            }
                        }
                    },
                    error: function(xhr) {
                        $(".submit_button").html(window.dashboardSendText || "Send").attr('disabled', false);
                        $(".text-danger").remove();
                        $('.store input').removeClass('border-danger');
                        $('.store select').removeClass('border-danger');
                        $('.store textarea').removeClass('border-danger');

                        var toastLines = [];

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            toastLines = collectValidationMessages(xhr.responseJSON.errors);
                            showValidationToast(toastLines);

                            $.each(xhr.responseJSON.errors, function(key, value) {
                                // if key has "." it means that input has two languages do this action to handle input name
                                if (key.indexOf(".") >= 0) {
                                    var split = key.split('.');
                                    key = split[0] + '\\[' + split[1] + '\\]';
                                }

                                var msgText = $.isArray(value) ? value[0] : value;

                                $('.store .after .error.' + key).append(
                                    `<span class="mt-5 text-danger">${msgText}</span>`);

                                // normal inputs
                                $('.store input[name^=' + key + ']').addClass('border-danger');
                                $('.store input[name^=' + key + '][type!=file]').parent().after(
                                    `<span class="mt-5 text-danger">${msgText}</span>`);

                                // for textarea
                                $('.store textarea[name^=' + key + ']').addClass('border-danger');
                                $('.store textarea[name^=' + key + ']').after(
                                    `<span class="mt-5 text-danger">${msgText}</span>`);

                                // for select input
                                $('.store select[name^=' + key + ']').addClass('border-danger');
                                $('.store select[name^=' + key + ']').parent().after(
                                    `<span class="mt-5 text-danger">${msgText}</span>`);
                            });
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            toastLines = [xhr.responseJSON.message];
                            showValidationToast(toastLines);
                        } else if (xhr.status >= 400) {
                            showValidationToast([window.dashboardGenericError || 'Error']);
                        }
                    },
                });
            });
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSubmitForm);
    } else {
        initSubmitForm();
    }
})();
