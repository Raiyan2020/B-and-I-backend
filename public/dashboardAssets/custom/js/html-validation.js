(function() {
    'use strict';

    var requiredMessage = window.dashboardRequiredFieldMessage || 'هذا الحقل مطلوب';

    function getRadiosByName(form, name) {
        return Array.prototype.filter.call(form.querySelectorAll('input[type="radio"]'), function(radio) {
            return radio.name === name;
        });
    }

    function hasCheckedRadio(field) {
        if (!field.name) {
            return field.checked;
        }

        var form = field.form || document;
        var radios = getRadiosByName(form, field.name);

        return Array.prototype.some.call(radios, function(radio) {
            return radio.checked;
        });
    }

    function isEmptyRequiredField(field) {
        if (!field.required || field.disabled) {
            return false;
        }

        if (field.type === 'radio') {
            return !hasCheckedRadio(field);
        }

        if (field.type === 'checkbox') {
            return !field.checked;
        }

        return !String(field.value || '').trim();
    }

    function updateValidityMessage(field) {
        if (!field || typeof field.setCustomValidity !== 'function') {
            return;
        }

        field.setCustomValidity(isEmptyRequiredField(field) ? requiredMessage : '');
    }

    function updateRelatedRadioMessages(field) {
        if (!field || field.type !== 'radio' || !field.name) {
            return;
        }

        var form = field.form || document;
        getRadiosByName(form, field.name).forEach(updateValidityMessage);
    }

    function initRequiredValidationMessages() {
        document.querySelectorAll('input[required], select[required], textarea[required]').forEach(updateValidityMessage);

        document.addEventListener('invalid', function(event) {
            updateValidityMessage(event.target);
        }, true);

        document.addEventListener('input', function(event) {
            updateValidityMessage(event.target);
        }, true);

        document.addEventListener('change', function(event) {
            updateValidityMessage(event.target);
            updateRelatedRadioMessages(event.target);
        }, true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRequiredValidationMessages);
    } else {
        initRequiredValidationMessages();
    }
})();
