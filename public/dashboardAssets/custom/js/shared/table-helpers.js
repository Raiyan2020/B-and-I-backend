/**
 * Table Helpers
 * Utility functions for table rendering
 */

(function() {
    'use strict';

    window.TableHelpers = {
        /**
         * Render image column for DataTables
         * @param {string} data - Image URL or filename
         * @param {string} defaultIcon - Icon class for placeholder (e.g., 'feather icon-user')
         * @param {string} alt - Alt text
         * @returns {string} HTML string
         */
        renderImage: function(data, defaultIcon, alt) {
            defaultIcon = defaultIcon || 'feather icon-image';
            alt = alt || 'Image';
            
            if (data && data !== null && data !== '' && data !== undefined) {
                return `<img class="table-image" src="${data}" alt="${alt}" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'table-image-placeholder\\'><i class=\\'${defaultIcon}\\'></i></div>';" />`;
            }
            
            return `<div class="table-image-placeholder">
                        <i class="${defaultIcon}"></i>
                    </div>`;
        },

        /**
         * Render badge for status
         * @param {mixed} value - Status value
         * @param {object} options - Configuration {active: {value, text, class}, inactive: {value, text, class}}
         * @returns {string} HTML string
         */
        renderBadge: function(value, options) {
            options = options || {
                active: { value: 1, text: window.dashboardActive || 'Active', class: 'success' },
                inactive: { value: 0, text: window.dashboardInActive || 'Inactive', class: 'danger' }
            };

            var isActive = (value == options.active.value || value === true || value === 'active');
            var config = isActive ? options.active : options.inactive;
            
            return `<span class="badge badge-${config.class}">${config.text}</span>`;
        },

        /**
         * Render date with formatting
         * @param {string} date - Date string
         * @param {string} format - Date format (default: 'Y-m-d h:i A')
         * @returns {string} Formatted date
         */
        renderDate: function(date, format) {
            if (!date) return '-';
            
            format = format || 'Y-m-d h:i A';
            
            try {
                var d = new Date(date);
                // Simple format conversion (for more complex, use moment.js or date-fns)
                var year = d.getFullYear();
                var month = String(d.getMonth() + 1).padStart(2, '0');
                var day = String(d.getDate()).padStart(2, '0');
                var hours = String(d.getHours()).padStart(2, '0');
                var minutes = String(d.getMinutes()).padStart(2, '0');
                var ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;
                
                return format
                    .replace('Y', year)
                    .replace('m', month)
                    .replace('d', day)
                    .replace('h', hours)
                    .replace('i', minutes)
                    .replace('A', ampm);
            } catch (e) {
                return date;
            }
        },

        /**
         * Render translatable field
         * @param {object} data - Object with locale keys (e.g., {ar: '...', en: '...'})
         * @param {string} locale - Current locale
         * @returns {string} Translated text
         */
        renderTranslatable: function(data, locale) {
            if (!data || typeof data !== 'object') return data || '-';
            
            locale = locale || (window.dashboardLocale || 'en');
            
            if (data[locale]) {
                return data[locale];
            }
            
            // Fallback to first available locale
            var firstKey = Object.keys(data)[0];
            return data[firstKey] || '-';
        }
    };
})();
