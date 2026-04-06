<x-dashboard.layouts.master title="{{ __('dashboard.account settings') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.personal information') }}">
            </x-dashboard.layouts.breadcrumb>

            @if (\Session::get('success'))
                <x-dashboard.layouts.message />
            @endif

            <div class="row mt-3">
                <!-- Profile Info Card -->
                <div class="col-12 col-lg-4">
                    <div class="card profile-info-card">
                        <div class="card-header text-center pb-0">
                            <div class="profile-avatar-wrapper">
                                @if ($admin->image)
                                    <img class="profile-avatar" src="{{ $admin->image }}" alt="{{ $admin->name }}">
                                @else
                                    <div class="profile-avatar-placeholder">
                                        <i class="feather icon-user"></i>
                                    </div>
                                @endif
                            </div>
                            <h4 class="card-title mt-2 mb-1">{{ $admin->name }}</h4>
                            <p class="text-muted mb-0">
                                <span
                                    class="badge badge-danger">{{ $admin->roles->first()?->display_name ?? __('dashboard.no role') }}</span>
                            </p>
                        </div>
                        <div class="card-content">
                            <div class="card-body pt-2">
                                <div class="profile-info-item">
                                    <div class="profile-info-icon">
                                        <i class="feather icon-mail"></i>
                                    </div>
                                    <div class="profile-info-content">
                                        <span class="profile-info-label">{{ __('dashboard.table email') }}</span>
                                        <span class="profile-info-value">{{ $admin->email }}</span>
                                    </div>
                                </div>
                                <div class="profile-info-item">
                                    <div class="profile-info-icon">
                                        <i class="feather icon-phone"></i>
                                    </div>
                                    <div class="profile-info-content">
                                        <span class="profile-info-label">{{ __('dashboard.table phone') }}</span>
                                        <span
                                            class="profile-info-value">{{ $admin->phone ?? __('dashboard.not set') }}</span>
                                    </div>
                                </div>
                                <div class="profile-info-item">
                                    <div class="profile-info-icon">
                                        <i class="feather icon-shield"></i>
                                    </div>
                                    <div class="profile-info-content">
                                        <span class="profile-info-label">{{ __('dashboard.role name') }}</span>
                                        <span
                                            class="profile-info-value">{{ $admin->roles->first()?->display_name ?? __('dashboard.no role') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Form Card -->
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="feather icon-edit-2 mr-1"></i>
                                {{ __('dashboard.edit personal information') }}
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <form class="form form-vertical" method="POST"
                                    action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-body">
                                        <div class="row">
                                            <!-- Image Upload -->
                                            <div class="col-12 mb-3">
                                                <div class="form-group">
                                                    <label for="image-input">{{ __('dashboard.table image') }}</label>
                                                    <div class="position-relative has-icon-left">
                                                        <input type="file" id="image-input" class="form-control"
                                                            name="image" accept="image/*">
                                                        <div class="form-control-position">
                                                            <i class="feather icon-image"></i>
                                                        </div>
                                                    </div>
                                                    @error('image')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                    @if ($admin->image)
                                                        <div class="mt-2">
                                                            <img src="{{ $admin->image }}" alt="Current"
                                                                class="img-thumbnail"
                                                                style="max-width: 150px; border-radius: 50%;">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <!-- Name -->
                                            <div class="col-12 col-md-6 mb-2">
                                                <div class="form-group">
                                                    <label for="name-input">{{ __('dashboard.table name') }}</label>
                                                    <div class="position-relative has-icon-left">
                                                        <input type="text" id="name-input"
                                                            value="{{ old('name', $admin->name) }}"
                                                            class="form-control" name="name"
                                                            placeholder="{{ __('dashboard.table name') }}" required>
                                                        <div class="form-control-position">
                                                            <i class="feather icon-user"></i>
                                                        </div>
                                                    </div>
                                                    @error('name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Email -->
                                            <div class="col-12 col-md-6 mb-2">
                                                <div class="form-group">
                                                    <label for="email-input">{{ __('dashboard.table email') }}</label>
                                                    <div class="position-relative has-icon-left">
                                                        <input type="email" id="email-input"
                                                            value="{{ old('email', $admin->email) }}"
                                                            class="form-control" name="email"
                                                            placeholder="{{ __('dashboard.table email') }}" required>
                                                        <div class="form-control-position">
                                                            <i class="feather icon-mail"></i>
                                                        </div>
                                                    </div>
                                                    @error('email')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Phone -->
                                            <div class="col-12 col-md-6 mb-2">
                                                <div class="form-group">
                                                    <label for="phone-input">{{ __('dashboard.table phone') }}</label>
                                                    <div class="position-relative has-icon-left">
                                                        <input type="text" id="phone-input" class="form-control"
                                                            value="{{ old('phone', $admin->phone) }}" name="phone"
                                                            placeholder="{{ __('dashboard.table phone') }}">
                                                        <div class="form-control-position">
                                                            <i class="fa fa-phone"></i>
                                                        </div>
                                                    </div>
                                                    @error('phone')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Role -->
                                            <div class="col-12 col-md-6 mb-2">
                                                <div class="form-group">
                                                    <label for="role-input">{{ __('dashboard.role name') }}</label>
                                                    <div class="position-relative has-icon-left">
                                                        <select id="role-input" class="form-control select2"
                                                            name="role">
                                                            <option value="">{{ __('dashboard.select role') }}
                                                            </option>
                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role->id }}"
                                                                    {{ $admin->hasRole($role->name) ? 'selected' : '' }}>
                                                                    {{ $role->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="form-control-position">
                                                            <i class="feather icon-shield"></i>
                                                        </div>
                                                    </div>
                                                    @error('role')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Password -->
                                            <div class="col-12 col-md-6 mb-2">
                                                <div class="form-group">
                                                    <label
                                                        for="password-input">{{ __('dashboard.table password') }}</label>
                                                    <div class="position-relative has-icon-left">
                                                        <input type="password" id="password-input"
                                                            class="form-control" name="password"
                                                            placeholder="{{ __('dashboard.table password') }}">
                                                        <div class="form-control-position">
                                                            <i class="fa fa-lock"></i>
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="feather icon-info"></i>
                                                        {{ __('dashboard.leave blank to keep current password') }}
                                                    </small>
                                                    @error('password')
                                                        <span class="text-danger d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Confirm Password -->
                                            <div class="col-12 col-md-6 mb-2">
                                                <div class="form-group">
                                                    <label
                                                        for="password-confirmation-input">{{ __('dashboard.table confirm password') }}</label>
                                                    <div class="position-relative has-icon-left">
                                                        <input type="password" id="password-confirmation-input"
                                                            class="form-control" name="password_confirmation"
                                                            placeholder="{{ __('dashboard.table confirm password') }}">
                                                        <div class="form-control-position">
                                                            <i class="fa fa-lock"></i>
                                                        </div>
                                                    </div>
                                                    @error('password_confirmation')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Submit Buttons -->
                                            <div class="col-12 mt-3 text-center">
                                                <button type="submit" class="btn btn-primary mr-1 mb-1">
                                                    <i class="feather icon-save mr-1"></i>
                                                    {{ __('dashboard.submit') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('styles')
        <style>
            /* Profile Page Styles */
            .profile-info-card {
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }

            .profile-avatar-wrapper {
                position: relative;
                display: inline-block;
                margin-bottom: 1rem;
            }

            .profile-avatar {
                width: 120px;
                height: 120px;
                border-radius: 50%;
                object-fit: cover;
                border: 4px solid #fff;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .profile-avatar-placeholder {
                width: 120px;
                height: 120px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                border: 4px solid #fff;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .profile-avatar-placeholder i {
                font-size: 3.5rem;
                color: #fff;
            }

            .profile-info-item {
                display: flex;
                align-items: flex-start;
                padding: 1rem 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .profile-info-item:last-child {
                border-bottom: none;
            }

            .profile-info-icon {
                width: 40px;
                height: 40px;
                border-radius: 8px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 1rem;
                margin-left: 15px;
                flex-shrink: 0;
            }

            .profile-info-icon i {
                color: #fff;
                font-size: 1.1rem;
            }

            .profile-info-content {
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            .profile-info-label {
                font-size: 0.85rem;
                color: #6c757d;
                margin-bottom: 0.25rem;
                font-weight: 500;
            }

            .profile-info-value {
                font-size: 0.95rem;
                color: #212529;
                font-weight: 600;
            }

            .card {
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                border: none;
            }

            .card-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #fff;
                border-radius: 12px 12px 0 0;
                padding: 1.25rem 1.5rem;
                border-bottom: none;
            }

            .card-header .card-title {
                color: #fff;
                margin: 0;
                font-weight: 600;
            }

            .form-group label {
                font-weight: 600;
                color: #495057;
                margin-bottom: 0.5rem;
                font-size: 0.9rem;
            }

            .form-control {
                border-radius: 8px;
                border: 1px solid #e0e0e0;
                padding: 0.75rem 1rem 0.75rem 2.5rem;
                transition: all 0.3s ease;
            }

            .form-control:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            }

            .btn-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                border-radius: 8px;
                padding: 0.75rem 1.5rem;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            }

            .btn-outline-warning {
                border-radius: 8px;
                padding: 0.75rem 1.5rem;
                font-weight: 600;
            }

            @media (max-width: 991px) {
                .profile-info-card {
                    margin-bottom: 2rem;
                }
            }
        </style>
    @endsection

    @section('script')
        <script>
            // Images Types
            const imagesTypes = ["jpeg", "png", "svg", "gif", "webp"];

            function openFileInput(id) {
                const fileInput = document.querySelector(id);
                if (fileInput) {
                    fileInput.click();
                }
            }

            function changeFileInput(dz, lt, pi, ufc, uf, ufi, ua, fd, ufn, ufit, event) {
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

                uploadFile(file, dropZoon, loadingText, previewImage, uploadedFile, uploadedFileInfo, uploadArea, fileDetails,
                    uploadedFileName, uploadedFileCounter, uploadedFileIconText);
            }

            function uploadFile(file, dropZoon, loadingText, previewImage, uploadedFile, uploadedFileInfo, uploadArea,
                fileDetails, uploadedFileName, uploadedFileCounter, uploadedFileIconText) {
                const fileReader = new FileReader();
                const fileType = file.type;
                const fileSize = file.size;

                if (fileValidate(fileType, fileSize, uploadedFileIconText)) {
                    dropZoon.classList.add('drop-zoon--Uploaded');
                    loadingText.style.display = "block";
                    previewImage.style.display = 'none';
                    uploadedFile.classList.remove('uploaded-file--open');
                    uploadedFileInfo.classList.remove('uploaded-file__info--active');

                    fileReader.addEventListener('load', function() {
                        setTimeout(function() {
                            uploadArea.classList.add('upload-area--open');
                            loadingText.style.display = "none";
                            previewImage.style.display = 'block';
                            fileDetails.classList.add('file-details--open');
                            uploadedFile.classList.add('uploaded-file--open');
                            uploadedFileInfo.classList.add('uploaded-file__info--active');
                        }, 500);

                        previewImage.setAttribute('src', fileReader.result);
                        progressMove(uploadedFileCounter);
                    });

                    fileReader.readAsDataURL(file);
                }
            }

            function progressMove(uploadedFileCounter) {
                let counter = 0;
                setTimeout(() => {
                    let counterIncrease = setInterval(() => {
                        if (counter === 100) {
                            clearInterval(counterIncrease);
                        } else {
                            counter = counter + 10;
                            if (uploadedFileCounter) {
                                uploadedFileCounter.innerHTML = `${counter}%`
                            }
                        }
                    }, 100);
                }, 600);
            }

            function fileValidate(fileType, fileSize, uploadedFileIconText) {
                let isImage = imagesTypes.filter((type) => fileType.indexOf(`image/${type}`) !== -1);

                if (isImage[0] === 'jpeg') {
                    if (uploadedFileIconText) {
                        uploadedFileIconText.innerHTML = 'jpg';
                    }
                } else {
                    if (uploadedFileIconText) {
                        uploadedFileIconText.innerHTML = isImage[0];
                    }
                }

                if (isImage.length !== 0) {
                    if (fileSize <= 2000000) {
                        return true;
                    } else {
                        alert('Please Your File Should be 2 Megabytes or Less');
                        return false;
                    }
                } else {
                    alert('Please make sure to upload An Image File Type');
                    return false;
                }
            }

            // Show existing image if available
            @if ($admin->image)
                document.addEventListener('DOMContentLoaded', function() {
                    const dropZoon = document.querySelector('#dropZoonProfile');
                    const previewImage = document.querySelector('#previewImageProfile');
                    if (dropZoon && previewImage && previewImage.src) {
                        dropZoon.classList.add('drop-zoon--Uploaded');
                        previewImage.style.display = 'block';
                    }
                });
            @endif
        </script>
    @endsection
</x-dashboard.layouts.master>
