/**
 * Country Code Selector with Phone Validation
 * Shared JavaScript for country code selector component
 */
(function() {
    'use strict';

    window.CountryCodeSelector = {
        /**
         * Initialize country code selector
         */
        init: function(selectorId, phoneInputId) {
            var $selector = $('#' + selectorId);
            var $phoneInput = $('#' + phoneInputId);

            if (!$selector.length || !$phoneInput.length) {
                return;
            }

            // Initialize Select2 if not already initialized
            if (!$selector.hasClass('select2-hidden-accessible')) {
                $selector.select2({
                    templateResult: this.formatCountry,
                    templateSelection: this.formatCountry
                });
            }

            // Update phone placeholder and validation on country change
            $selector.off('change.countrySelector').on('change.countrySelector', function() {
                var defaultPlaceholder = (window.dashboardTranslations && window.dashboardTranslations.table_phone) 
                    ? window.dashboardTranslations.table_phone 
                    : 'Phone';
                $phoneInput.attr('placeholder', defaultPlaceholder);
                $phoneInput.removeAttr('data-phone-start');
                $phoneInput.removeAttr('pattern');
                $phoneInput.off('input.phoneValidation');
                $phoneInput.removeClass('is-invalid');
                $phoneInput.next('.phone-start-error').remove();
            });

            // Trigger change on page load if country is already selected
            if ($selector.val()) {
                $selector.trigger('change.countrySelector');
            }
        },

        /**
         * Format country option with flag
         */
        formatCountry: function(country) {
            if (!country.id) {
                return country.text;
            }
            var flag = $(country.element).data('flag');
            var $country = $(
                '<span><span class="flag-icon flag-icon-' + flag + ' flag-icon-squared mr-1"></span> ' + country.text + '</span>'
            );
            return $country;
        }
    };

    // Auto-initialize on document ready
    $(document).ready(function() {
        $('.country-code-selector').each(function() {
            var selectorId = $(this).attr('id');
            var phoneInputId = $(this).data('phone-input');
            if (selectorId && phoneInputId) {
                window.CountryCodeSelector.init(selectorId, phoneInputId);
            }
        });
    });
})();
