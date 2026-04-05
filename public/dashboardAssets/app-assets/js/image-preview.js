// Image Preview Handler for all image inputs
(function() {
    'use strict';

    function initImagePreview() {
        // Find all file inputs with accept="image/*" or name="image"
        const imageInputs = document.querySelectorAll('input[type="file"][name="image"], input[type="file"][accept*="image"]');

        imageInputs.forEach(function(input) {
            // Skip if already initialized
            if (input.dataset.previewInitialized === 'true') {
                return;
            }

            input.dataset.previewInitialized = 'true';

            const wrapper = input.closest('.form-group') || input.parentElement;
            const label = wrapper.querySelector('label');
            const existingImage = input.dataset.existingImage || (input.closest('form').querySelector('[data-existing-image]')?.dataset.existingImage);

            // Create preview container
            // const previewContainer = document.createElement('div');
            // previewContainer.className = 'image-preview-container';
            // previewContainer.id = 'image-preview-' + (input.id || Math.random().toString(36).substr(2, 9));

            // Create placeholder
            const placeholder = document.createElement('div');
            placeholder.className = 'image-preview-placeholder';
            placeholder.innerHTML = '<i class="feather icon-image"></i><p>{{__("dashboard.click_to_upload_image")}}</p>';

            // Create remove button
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'image-remove-btn';
            removeBtn.innerHTML = '<i class="feather icon-x"></i>';
            removeBtn.style.display = 'none';

            // Create preview image
            const previewImg = document.createElement('img');
            previewImg.style.display = 'none';

            previewContainer.appendChild(previewImg);
            previewContainer.appendChild(placeholder);
            previewContainer.appendChild(removeBtn);

            // Insert preview container before the input wrapper
            const inputWrapper = input.closest('.position-relative') || input.parentElement;
            inputWrapper.parentElement.insertBefore(previewContainer, inputWrapper);

            // Show existing image if available
            if (existingImage) {
                previewImg.src = existingImage;
                previewImg.style.display = 'block';
                placeholder.style.display = 'none';
                removeBtn.style.display = 'flex';
            }

            // Handle click on preview container
            previewContainer.addEventListener('click', function(e) {
                if (e.target !== removeBtn && !removeBtn.contains(e.target)) {
                    input.click();
                }
            });

            // Handle file change
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewImg.style.display = 'block';
                        placeholder.style.display = 'none';
                        removeBtn.style.display = 'flex';
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Handle remove button
            removeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                input.value = '';
                previewImg.src = '';
                previewImg.style.display = 'none';
                placeholder.style.display = 'block';
                removeBtn.style.display = 'none';
            });
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initImagePreview);
    } else {
        initImagePreview();
    }

    // Re-initialize after AJAX content loads
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('ajaxComplete', initImagePreview);
    }
})();

