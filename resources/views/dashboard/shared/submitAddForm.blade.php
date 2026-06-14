<script>
    $(document).ready(function() {
        // Handle both store and update forms
        $(document).on('submit', '.store, form[method="POST"]', function(e) {
            e.preventDefault();

            var $form = $(this);
            var url = $form.attr('action');
            var method = $form.find('input[name="_method"]').val() || 'POST';
            var $submitBtn = $form.find('.submit_button');
            var originalBtnText = $submitBtn.html();

            // Clear previous errors
            $form.find('.text-danger').remove();
            $form.find('input, textarea, select').removeClass('border-danger');

            var $companyLicense = $form.find('#company-license');
            if ($companyLicense.length && $companyLicense[0].files[0]) {
                var licenseFile = $companyLicense[0].files[0];
                var licenseMaxSize = (window.companyLicenseMaxSizeMb || 2) * 1024 * 1024;
                var licenseTranslations = window.dashboardTranslations || {};
                var allowedLicenseTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];

                if (allowedLicenseTypes.indexOf(licenseFile.type) === -1) {
                    var licenseTypeError = licenseTranslations.company_license_file_type_error ||
                        '{{ __('dashboard.company_license_file_type_error') }}';
                    $companyLicense.addClass('border-danger');
                    $companyLicense.closest('.form-group').append(
                        '<span class="text-danger d-block mt-1">' + licenseTypeError + '</span>'
                    );
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            position: "top-start",
                            icon: "error",
                            title: licenseTypeError,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                    return;
                }

                if (licenseFile.size > licenseMaxSize) {
                    var licenseSizeError = licenseTranslations.company_license_file_size_error ||
                        '{{ __('dashboard.company_license_file_size_error') }}';
                    $companyLicense.addClass('border-danger');
                    $companyLicense.closest('.form-group').append(
                        '<span class="text-danger d-block mt-1">' + licenseSizeError + '</span>'
                    );
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            position: "top-start",
                            icon: "error",
                            title: licenseSizeError,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                    return;
                }
            }

            $.ajax({
                url: url,
                method: method === 'PUT' || method === 'PATCH' ? 'POST' : 'POST',
                data: new FormData($form[0]),
                dataType: 'json',
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $submitBtn.html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                    ).attr('disabled', true);
                },
                success: function(response) {
                    $submitBtn.html(originalBtnText).attr('disabled', false);

                    Swal.fire({
                        position: "top-start",
                        icon: "success",
                        title: response.msg || '{{ __('dashboard.item added successfully') }}',
                        showConfirmButton: false,
                        buttonsStyling: false,
                        timer: 1500
                    });

                    if (response.url) {
                        setTimeout(function() {
                            window.location.replace(response.url);
                        }, 1000);
                    } else if (response.redirect === false) {
                        return;
                    } else {
                        if (typeof window.resetDashboardForm === 'function') {
                            window.resetDashboardForm($form);
                        } else {
                            $form.trigger("reset");
                        }
                        // Reload DataTable if exists
                        var $table = $form.closest('.card').next().find('table');
                        if ($.fn.DataTable && $.fn.DataTable.isDataTable($table)) {
                            $table.DataTable().ajax.reload(null, false);
                        }
                    }
                },
                error: function(xhr) {
                    $submitBtn.html(originalBtnText).attr('disabled', false);

                    // Clear previous errors
                    $form.find('.text-danger').remove();
                    $form.find('input, textarea, select').removeClass('border-danger');

                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        // Track if we've shown toast for image/photo errors
                        var imageErrorShown = false;

                        $.each(xhr.responseJSON.errors, function(key, value) {
                            var errorMessage = Array.isArray(value) ? value[0] : value;

                            // Check if this is an image/photo validation error and show toast
                            var keyLower = key.toLowerCase();
                            if (!imageErrorShown && (keyLower.indexOf('image') !== -1 || keyLower.indexOf('photo') !== -1 || keyLower === 'company_license')) {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        position: "top-start",
                                        icon: "error",
                                        title: errorMessage,
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                    imageErrorShown = true;
                                }
                            }

                            // Default error HTML
                            var errorHtml = `<span class="text-danger d-block mt-1">${errorMessage}</span>`;
                            // For image components, use text-center to match @error directive styling
                            var imageErrorHtml = `<span class="text-danger d-block text-center mt-2">${errorMessage}</span>`;

                            // Handle keys with dots (e.g., "name.ar", "image0")
                            var selectorKey = key;
                            if (key.indexOf(".") >= 0) {
                                var split = key.split('.');
                                // If it's a language key (e.g., "name.ar")
                                if (split.length === 2 && (split[1] === 'ar' || split[1] === 'en')) {
                                    selectorKey = split[0] + '\\[' + split[1] + '\\]';
                                } else {
                                    // For image components (e.g., "image0")
                                    selectorKey = key.replace('.', '');
                                }
                            }

                            // Try to find the input/select/textarea
                            // For image components, name might be "image", "image0", "image1", etc.
                            var baseKey = key.replace(/\d+$/, ''); // Remove trailing numbers (e.g., "image0" -> "image")

                            // Try multiple patterns to find the input - check all file inputs first
                            // Check exact match, then base key, then starts with patterns
                            var $input = $form.find('input[type="file"][name="' + key + '"]');
                            if (!$input.length) {
                                $input = $form.find('input[type="file"][name="' + baseKey + '"]');
                            }
                            if (!$input.length) {
                                $input = $form.find('input[type="file"][name^="' + key + '"]');
                            }
                            if (!$input.length) {
                                $input = $form.find('input[type="file"][name^="' + baseKey + '"]');
                            }

                            // If still not found, try without type filter (exact name only)
                            if (!$input.length) {
                                $input = $form.find('input[name="' + key + '"], input[name="' + baseKey + '"]');
                            }

                            var $select = $form.find('select[name="' + key + '"], select[name="' + selectorKey + '"]');
                            var $textarea = $form.find('textarea[name="' + key + '"], textarea[name="' + selectorKey + '"]');

                            // Handle file inputs (image components)
                            if ($input.length && $input.attr('type') === 'file') {
                                $input.addClass('border-danger');
                                // Find the upload-image-container
                                var $container = $input.closest('.upload-image-container');
                                var $colWrapper = $container.closest('.col-12, .col-6, .col-md-12, .col-md-6');

                                if ($colWrapper.length && $container.length) {
                                    // Remove any existing error in this column
                                    $colWrapper.find('.text-danger').remove();
                                    // Add error after upload-image-container (same place as @error directive in component)
                                    // The @error directive is placed after </div> of upload-image-container, inside col-12
                                    // Use imageErrorHtml to match the styling of @error directive (text-center mt-2)
                                    $container.after(imageErrorHtml);
                                } else if ($container.length) {
                                    // Fallback: add after container
                                    $container.after(imageErrorHtml);
                                } else {
                                    // Last resort: try to find by looking for upload-image-container near file inputs
                                    var $fileInputs = $form.find('input[type="file"]');
                                    $fileInputs.each(function() {
                                        var $fileInput = $(this);
                                        var inputName = $fileInput.attr('name') || '';
                                        // Check if this input name matches the error key
                                        if (inputName === key || inputName === baseKey || inputName.indexOf(key) === 0 || inputName.indexOf(baseKey) === 0) {
                                            var $nearContainer = $fileInput.closest('.upload-image-container');
                                            if ($nearContainer.length) {
                                                $nearContainer.after(imageErrorHtml);
                                                return false; // break
                                            }
                                        }
                                    });

                                    // If still not found, add after input's parent
                                    if (!$colWrapper.find('.text-danger').length) {
                                        var $parent = $input.closest('.form-group, .col-12, .col-6');
                                        if ($parent.length) {
                                            $parent.append(errorHtml);
                                        } else {
                                            $input.parent().after(errorHtml);
                                        }
                                    }
                                }
                            }
                            // Handle regular inputs
                            else if ($input.length && $input.attr('type') !== 'file') {
                                $input.addClass('border-danger');
                                var $parent = $input.parent();
                                // If input has form-control-position, add error after parent
                                if ($parent.hasClass('position-relative') || $parent.hasClass('has-icon-left')) {
                                    $parent.after(errorHtml);
                                } else {
                                    $input.after(errorHtml);
                                }
                            }
                            // Handle select inputs
                            else if ($select.length) {
                                $select.addClass('border-danger');
                                var $selectParent = $select.parent();
                                if ($selectParent.hasClass('form-group')) {
                                    $selectParent.append(errorHtml);
                                } else {
                                    $select.after(errorHtml);
                                }
                            }
                            // Handle textarea
                            else if ($textarea.length) {
                                $textarea.addClass('border-danger');
                                $textarea.after(errorHtml);
                            }
                            // Fallback: add error at the end of form
                            else {
                                // Try to find by name attribute with different patterns
                                var $fallbackInput = $form.find('[name*="' + key.split('.')[0] + '"]');
                                if ($fallbackInput.length) {
                                    $fallbackInput.addClass('border-danger');
                                    $fallbackInput.closest('.form-group, .col-12, .col-6').append(errorHtml);
                                } else {
                                    // Last resort: add at form end
                                    $form.find('.form-body').append('<div class="col-12">' + errorHtml + '</div>');
                                }
                            }
                        });
                    } else {
                        // General error message
                        var errorMsg = xhr.responseJSON?.message || '{{ __('dashboard.error loading data') }}';
                        Swal.fire({
                            position: "top-start",
                            icon: "error",
                            title: errorMsg,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                },
            });
        });
    });
</script>
