/**
 * Submit Add Form Handler
 * Handles AJAX form submissions for .store forms
 */

(function() {
    'use strict';

    function appendFieldError($field, msgText) {
        if (!$field.length) {
            return;
        }

        $field.addClass('border-danger');

        var $parent = $field.parent();
        if ($parent.hasClass('position-relative') || $parent.hasClass('has-icon-left')) {
            $parent.after('<span class="mt-5 text-danger">' + msgText + '</span>');
            return;
        }

        if ($field.is('select') && $parent.hasClass('form-group')) {
            $parent.append('<span class="mt-5 text-danger">' + msgText + '</span>');
            return;
        }

        $field.after('<span class="mt-5 text-danger">' + msgText + '</span>');
    }

    function resolveFieldSelector(key) {
        if (key.indexOf('.') >= 0) {
            var split = key.split('.');
            return split[0] + '\\[' + split[1] + '\\]';
        }

        return key;
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
                        form.find('.submit_button').html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                        ).attr('disabled', true);
                    },
                    success: function(response) {
                        form.find('.text-danger').remove();
                        form.find('input, select, textarea').removeClass('border-danger');
                        form.find('.submit_button').html(window.dashboardSendText || 'Send').attr('disabled', false);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                position: 'top-start',
                                icon: 'success',
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
                        }

                        if (typeof window.resetDashboardForm === 'function') {
                            window.resetDashboardForm(form);
                        } else {
                            form.trigger('reset');
                        }

                        var table = form.closest('.card').next().find('table');
                        if ($.fn.DataTable && $.fn.DataTable.isDataTable(table)) {
                            table.DataTable().ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        form.find('.submit_button').html(window.dashboardSendText || 'Send').attr('disabled', false);
                        form.find('.text-danger').remove();
                        form.find('input, select, textarea').removeClass('border-danger');

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                var selectorKey = resolveFieldSelector(key);
                                var msgText = $.isArray(value) ? value[0] : value;

                                form.find('.after .error.' + selectorKey).append(
                                    '<span class="mt-5 text-danger">' + msgText + '</span>'
                                );

                                appendFieldError(form.find('input[name="' + selectorKey + '"]'), msgText);
                                appendFieldError(form.find('textarea[name="' + selectorKey + '"]'), msgText);
                                appendFieldError(form.find('select[name="' + selectorKey + '"]'), msgText);
                            });
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(xhr.responseJSON.message, window.dashboardValidationTitle || 'Validation');
                            }
                        } else if (xhr.status >= 400 && typeof toastr !== 'undefined') {
                            toastr.error(window.dashboardGenericError || 'Error');
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
