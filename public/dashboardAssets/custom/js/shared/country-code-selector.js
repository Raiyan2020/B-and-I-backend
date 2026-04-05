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
                var selectedOption = $(this).find('option:selected');
                var phoneStart = selectedOption.data('phone-start');
                
                if (phoneStart) {
                    var placeholder = phoneStart + 'XXXXXXXX';
                    $phoneInput.attr('placeholder', placeholder);
                    $phoneInput.attr('data-phone-start', phoneStart);
                    $phoneInput.attr('pattern', '^' + phoneStart + '[0-9]+$');
                    
                    // Add custom validation
                    $phoneInput.off('input.phoneValidation').on('input.phoneValidation', function() {
                        var value = $(this).val();
                        if (value && !value.startsWith(phoneStart)) {
                            $(this).addClass('is-invalid');
                            // Remove existing error message if any
                            $(this).next('.phone-start-error').remove();
                            
                            // Build error message with phone start digit
                            var errorMsg = '';
                            if (window.dashboardTranslations && window.dashboardTranslations.phone_must_start_with) {
                                // Replace :start placeholder with the actual phone start digit
                                errorMsg = window.dashboardTranslations.phone_must_start_with.replace(':start', phoneStart);
                                // If :start was not found in the message, append the digit at the end
                                if (errorMsg === window.dashboardTranslations.phone_must_start_with) {
                                    errorMsg = errorMsg + ' ' + phoneStart;
                                }
                            } else {
                                errorMsg = 'Phone number must start with ' + phoneStart;
                            }
                            
                            $(this).after('<span class="text-danger phone-start-error" style="font-size: 0.875rem; display: block; margin-top: 0.25rem;">' + errorMsg + '</span>');
                        } else {
                            $(this).removeClass('is-invalid');
                            $(this).next('.phone-start-error').remove();
                        }
                    });
                } else {
                    var defaultPlaceholder = (window.dashboardTranslations && window.dashboardTranslations.table_phone) 
                        ? window.dashboardTranslations.table_phone 
                        : 'Phone';
                    $phoneInput.attr('placeholder', defaultPlaceholder);
                    $phoneInput.removeAttr('data-phone-start');
                    $phoneInput.removeAttr('pattern');
                    $phoneInput.off('input.phoneValidation');
                }
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
