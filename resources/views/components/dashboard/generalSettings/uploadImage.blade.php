@php
    use App\Models\GeneralSetting;
    $i = isset($i) ? $i : '';
    $col = isset($col) ? $col : '6';
    $src =
        $folder == 'header' || $folder == 'logo'
            ? asset('Site/assets/images/' . $folder . GeneralSetting::getValueForKey($name . $i))
            : $src;
            $display = $folder == 'header' || $folder == 'logo'? GeneralSetting::getValueForKey($name . $i) : $src;
@endphp
<div class="col-{{ $col }} mb-2">
    <div class="upload-image-container">
        <div id="{{ 'uploadArea' . $i }}" class="upload-area upload-area--circular">
            <div class="upload-area__header">
                <h4 class="upload-area__title--compact">{{ __('dashboard.' . $name) }}</h4>
                <p class="upload-area__paragraph upload-area__paragraph--compact">
                    {{ __('dashboard.uploaded_file_image') }}
                </p>
            </div>
            <div id="{{ 'dropZoon' . $i }}" onclick="openFileInput('#fileInput{{ $i }}')" role="button"
                tabindex="0"
                onkeypress="if(event.key==='Enter'||event.key===' '){event.preventDefault();openFileInput('#fileInput{{ $i }}');}"
                class="upload-area__drop-zoon drop-zoon drop-zoon--circular">
                <span class="drop-zoon__icon drop-zoon__icon--compact">
                    <i class='bx bxs-file-image'></i>
                </span>
                <p class="drop-zoon__paragraph drop-zoon__paragraph--compact">Click to browse</p>
                <span id="loadingText{{ $i }}" class="drop-zoon__loading-text">Please Wait</span>
                <img src="{{ $src }}" alt="Preview" id="previewImage{{ $i }}"
                    class="drop-zoon__preview-image drop-zoon__preview-image--circular" draggable="false"
                    style="display:{{ $display ? ' block' : 'none' }}">
                <input type="file" id="fileInput{{ $i }}" name="{{ $name . $i }}"
                    onchange="changeFileInput('dropZoon{{ $i }}','loadingText{{ $i }}','previewImage{{ $i }}','uploaded-file__counter{{ $i }}','uploadedFile{{ $i }}','uploadedFileInfo{{ $i }}','uploadArea{{ $i }}','fileDetails{{ $i }}','uploaded-file__name{{ $i }}','uploaded-file__icon-text{{ $i }}',event)"
                    class="drop-zoon__file-input" accept="image/*">
            </div>
            <!-- End Drop Zoon -->

            <!-- File Details -->
            <div id="fileDetails{{ $i }}" class="upload-area__file-details file-details">
                {{-- <h3 class="file-details__title file-details__title--compact">Uploaded File</h3> --}}

                <div id="uploadedFile{{ $i }}" class="uploaded-file">
                    <div class="uploaded-file__icon-container">
                        <i class='bx bxs-file-blank uploaded-file__icon uploaded-file__icon--compact'></i>
                        <span class="uploaded-file__icon-text{{ $i }}"></span>
                    </div>

                    <div id="uploadedFileInfo{{ $i }}" class="uploaded-file__info">
                        <span class="uploaded-file__name{{ $i }}"></span>
                        <span class="uploaded-file__counter{{ $i }}">0%</span>
                    </div>
                </div>
            </div>
            <!-- End File Details -->
        </div>

    </div>
</div>
@section('styles')
    <style>
        .upload-image-container {
            display: flex;
            justify-content: center;
        }

        .upload-area--circular {
            max-width: 14rem;
            padding: 1rem;
            border-radius: 16px;
            margin: auto;
        }

        .upload-area__title--compact {
            font-size: .95rem;
            margin-bottom: .25rem;
        }

        .upload-area__paragraph--compact {
            font-size: .7rem;
            margin: 0;
        }

        .drop-zoon--circular {
            width: 9rem;
            height: 9rem;
            border-radius: 50%;
            margin: .2rem auto 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .drop-zoon__icon--compact {
            font-size: 1.8rem;
            margin-bottom: .25rem;
        }

        .drop-zoon__paragraph--compact {
            font-size: .65rem;
            text-align: center;
        }

        .drop-zoon__preview-image--circular {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            position: absolute;
            inset: 0;
        }

        .drop-zoon__loading-text {
            font-size: .65rem;
        }
    </style>
@endsection

@section('script')
    <script>
        function fillTextarea() {
            var content = document.querySelector(".ql-editor").innerHTML;
            $('#copyright').empty();
            $('#copyright').val(content);
        }

        // Images Types
        const imagesTypes = ["jpeg", "png", "svg", "gif", "webp"];

        function openFileInput(id) {
            // Click The (fileInput)
            const fileInput = document.querySelector(id);
            fileInput.click();
        }

        // When (fileInput) has (change) Event
        function changeFileInput(dz, lt, pi, ufc, uf, ufi, ua, fd, ufn, ufit, event) {
            // Select The Chosen File
            const file = event.target.files[0];
            const dropZoon = document.querySelector('#' + dz);
            const loadingText = document.querySelector('#' + lt);
            const previewImage = document.querySelector('#' + pi);
            const uploadedFileCounter = document.querySelector('.' + ufc);
            const uploadedFile = document.querySelector('#' + uf);
            const uploadedFileInfo = document.querySelector('#' + ufi);
            const uploadArea = document.querySelector('#' + ua);
            const fileDetails = document.querySelector('#' + fd);
            const uploadedFileName = document.querySelector('.' + ufn);
            const uploadedFileIconText = document.querySelector('.' + ufit);

            // Call Function uploadFile(), And Send To Her The Chosen File :)
            uploadFile(file, dropZoon, loadingText, previewImage, uploadedFile, uploadedFileInfo, uploadArea, fileDetails,
                uploadedFileName, uploadedFileCounter, uploadedFileIconText);
        }

        // Upload File Function
        function uploadFile(file, dropZoon, loadingText, previewImage, uploadedFile, uploadedFileInfo, uploadArea,
            fileDetails, uploadedFileName, uploadedFileCounter, uploadedFileIconText) {
            // FileReader()
            const fileReader = new FileReader();
            // File Type
            const fileType = file.type;
            // File Size
            const fileSize = file.size;

            // If File Is Passed from the (File Validation) Function
            if (fileValidate(fileType, fileSize, uploadedFileIconText)) {
                // Add Class (drop-zoon--Uploaded) on (drop-zoon)

                dropZoon.classList.add('drop-zoon--Uploaded');

                // Show Loading-text
                loadingText.style.display = "block";
                // Hide Preview Image
                previewImage.style.display = 'none';

                // Remove Class (uploaded-file--open) From (uploadedFile)
                uploadedFile.classList.remove('uploaded-file--open');
                // Remove Class (uploaded-file__info--active) from (uploadedFileInfo)
                uploadedFileInfo.classList.remove('uploaded-file__info--active');

                // After File Reader Loaded
                fileReader.addEventListener('load', function() {
                    // After Half Second
                    setTimeout(function() {
                        // Add Class (upload-area--open) On (uploadArea)
                        uploadArea.classList.add('upload-area--open');

                        // Hide Loading-text (please-wait) Element
                        loadingText.style.display = "none";
                        // Show Preview Image
                        previewImage.style.display = 'block';

                        // Add Class (file-details--open) On (fileDetails)
                        fileDetails.classList.add('file-details--open');
                        // Add Class (uploaded-file--open) On (uploadedFile)
                        uploadedFile.classList.add('uploaded-file--open');
                        // Add Class (uploaded-file__info--active) On (uploadedFileInfo)
                        uploadedFileInfo.classList.add('uploaded-file__info--active');
                    }, 500); // 0.5s

                    // Add The (fileReader) Result Inside (previewImage) Source
                    previewImage.setAttribute('src', fileReader.result);

                    // Add File Name Inside Uploaded File Name
                    // uploadedFileName.innerHTML = file.name;

                    // Call Function progressMove();
                    progressMove(uploadedFileCounter);
                });

                // Read (file) As Data Url
                fileReader.readAsDataURL(file);
            } else { // Else

                this; // (this) Represent The fileValidate(fileType, fileSize) Function

            };
        };

        // Progress Counter Increase Function
        function progressMove(uploadedFileCounter) {
            // Counter Start
            let counter = 0;

            // After 600ms
            setTimeout(() => {
                // Every 100ms
                let counterIncrease = setInterval(() => {
                    // If (counter) is equle 100
                    if (counter === 100) {
                        // Stop (Counter Increase)
                        clearInterval(counterIncrease);
                    } else { // Else
                        // plus 10 on counter
                        counter = counter + 10;
                        // add (counter) vlaue inisde (uploadedFileCounter)
                        uploadedFileCounter.innerHTML = `${counter}%`
                    }
                }, 100);
            }, 600);
        };


        function showImageUploadAlert(message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: @json(__('dashboard.validation_errors_title')),
                    text: message,
                    confirmButtonText: @json(__('dashboard.confirm')),
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
                return;
            }

            alert(message);
        }

        // Simple File Validate Function
        function fileValidate(fileType, fileSize, uploadedFileIconText) {
            // File Type Validation
            let isImage = imagesTypes.filter((type) => fileType.indexOf(`image/${type}`) !== -1);

            // If The Uploaded File Type Is 'jpeg'
            if (isImage[0] === 'jpeg') {
                // Add Inisde (uploadedFileIconText) The (jpg) Value
                uploadedFileIconText.innerHTML = 'jpg';
            } else { // else
                // Add Inisde (uploadedFileIconText) The Uploaded File Type
                uploadedFileIconText.innerHTML = isImage[0];
            };

            // If The Uploaded File Is An Image
            if (isImage.length !== 0) {
                // Check, If File Size Is 2MB or Less
                if (fileSize <= 2000000) { // 2MB :)
                    return true;
                } else { // Else File Size
                    showImageUploadAlert(@json(__('dashboard.image_file_size_error')));
                    return false;
                };
            } else { // Else File Type
                showImageUploadAlert(@json(__('dashboard.image_file_type_error')));
                return false;
            };
        };

        // :)
    </script>
@endsection
