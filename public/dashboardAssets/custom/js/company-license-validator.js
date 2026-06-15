/**
 * Company license file validation for advertiser/company forms.
 */
(function() {
    'use strict';

    var ALLOWED_TYPES = [
        'image/jpeg',
        'image/png',
        'image/jpg',
        'application/pdf',
    ];

    function getMaxSizeBytes() {
        return (window.companyLicenseMaxSizeMb || 2) * 1024 * 1024;
    }

    function getTranslations() {
        return window.dashboardTranslations || {};
    }

    function showInlineError($input, message) {
        var $formGroup = $input.closest('.form-group');
        $formGroup.find('.text-danger').remove();
        $input.addClass('border-danger');
        $formGroup.append('<span class="text-danger d-block mt-1">' + message + '</span>');
    }

    function clearInlineError($input) {
        $input.removeClass('border-danger');
        $input.closest('.form-group').find('.text-danger').remove();
    }

    function showAlert(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                position: 'top-start',
                icon: 'error',
                title: message,
                showConfirmButton: false,
                timer: 3000,
            });
        }
    }

    function validateCompanyLicenseInput(input) {
        var $input = $(input);
        var file = input.files && input.files[0];
        var translations = getTranslations();

        clearInlineError($input);

        if (!file) {
            return true;
        }

        if (ALLOWED_TYPES.indexOf(file.type) === -1) {
            var typeError = translations.company_license_file_type_error || 'Invalid file type.';
            showInlineError($input, typeError);
            showAlert(typeError);
            input.value = '';
            return false;
        }

        if (file.size > getMaxSizeBytes()) {
            var sizeError = translations.company_license_file_size_error || 'File size must not exceed 2 MB.';
            showInlineError($input, sizeError);
            showAlert(sizeError);
            input.value = '';
            return false;
        }

        return true;
    }

    function init() {
        if (typeof jQuery === 'undefined') {
            setTimeout(init, 100);
            return;
        }

        var $ = jQuery;

        $(document).on('change', '#company-license', function() {
            validateCompanyLicenseInput(this);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
