/**
 * Dashboard Form Reset Handler
 * Clears all form fields including Select2, CKEditor, and image uploads.
 */
(function() {
    'use strict';

    function waitForJQuery(callback) {
        if (typeof jQuery !== 'undefined') {
            callback(jQuery);
            return;
        }
        setTimeout(function() {
            waitForJQuery(callback);
        }, 100);
    }

    function getDefaultSelectedIndex(selectEl) {
        for (var i = 0; i < selectEl.options.length; i++) {
            if (selectEl.options[i].defaultSelected) {
                return i;
            }
        }

        return 0;
    }

    function resetUploadImageContainer($container) {
        var $fileInput = $container.find('input[type="file"]');
        $fileInput.val('');

        var $preview = $container.find('[id^="previewImage"]');
        var defaultSrc = $preview.data('default-src') || '';
        var defaultDisplay = $preview.data('default-display');

        $preview.attr('src', defaultSrc);
        $preview.css('display', defaultDisplay === 'block' ? 'block' : 'none');

        $container.find('[id^="dropZoon"]').removeClass('drop-zoon--Uploaded');
        $container.find('[id^="uploadArea"]').removeClass('upload-area--open');
        $container.find('[id^="fileDetails"]').removeClass('file-details--open');
        $container.find('[id^="uploadedFile"]').removeClass('uploaded-file--open');
        $container.find('[id^="uploadedFileInfo"]').removeClass('uploaded-file__info--active');
        $container.find('[id^="loadingText"]').hide();
        $container.find('[class*="uploaded-file__counter"]').text('0%');
    }

    function resetSelectElement($select) {
        var selectEl = $select[0];
        var defaultIndex = $select.data('default-selected-index');

        if (defaultIndex === undefined) {
            defaultIndex = getDefaultSelectedIndex(selectEl);
        }

        selectEl.selectedIndex = defaultIndex;

        var $option = $select.find('option').eq(defaultIndex);
        var optionValue = $option.attr('value');

        if (optionValue !== undefined) {
            $select.val(optionValue);
        } else {
            $select.val($option.val());
        }

        if ($select.hasClass('select2-hidden-accessible')) {
            var select2Instance = $select.data('select2');
            var select2Options = select2Instance ? select2Instance.options : {
                dropdownAutoWidth: true,
                width: '100%'
            };

            $select.select2('destroy');
            $select.select2(select2Options);
            $select.trigger('change');
        }
    }

    function cacheFormDefaults($form) {
        if ($form.data('defaults-cached')) {
            return;
        }

        $form.find('.upload-image-container').each(function() {
            var $preview = $(this).find('[id^="previewImage"]');
            if ($preview.length) {
                $preview.data('default-src', $preview.attr('src') || '');
                $preview.data('default-display', $preview.is(':visible') ? 'block' : 'none');
            }
        });

        $form.find('textarea').each(function() {
            $(this).data('default-value', $(this).val() || '');
        });

        $form.find('select').each(function() {
            $(this).data('default-selected-index', getDefaultSelectedIndex(this));
        });

        $form.data('defaults-cached', true);
    }

    function resetDashboardForm($form) {
        if (!$form || !$form.length) {
            return;
        }

        cacheFormDefaults($form);

        $form.find('.text-danger').remove();
        $form.find('.phone-start-error').remove();
        $form.find('input, textarea, select').removeClass('border-danger is-invalid');

        $form[0].reset();

        $form.find('select').each(function() {
            resetSelectElement($(this));
        });

        $form.find('.country-code-selector, #admin-country-code').each(function() {
            $(this).trigger('change.countrySelector');
        });

        if (typeof CKEDITOR !== 'undefined') {
            $form.find('textarea').each(function() {
                if (this.id && CKEDITOR.instances[this.id]) {
                    CKEDITOR.instances[this.id].setData($(this).data('default-value') || '');
                }
            });
        }

        $form.find('.upload-image-container').each(function() {
            resetUploadImageContainer($(this));
        });
    }

    window.resetDashboardForm = resetDashboardForm;

    waitForJQuery(function($) {
        $(document).ready(function() {
            $('form:has(.btn-reset-form)').each(function() {
                cacheFormDefaults($(this));
            });

            $(document).on('click', '.btn-reset-form', function(e) {
                e.preventDefault();
                resetDashboardForm($(this).closest('form'));
            });
        });
    });
})();
