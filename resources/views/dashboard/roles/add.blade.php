@php
    $groupedPermissions = \App\Support\PermissionGroups::group($permissions);
@endphp

<x-dashboard.layouts.master title="{{__('dashboard.add roles')}}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <x-dashboard.layouts.breadcrumb now="{{__('dashboard.add roles')}}">
                <li class="breadcrumb-item"><a href="{{route('admin.roles.index')}}">{{__('dashboard.roles list')}}</a></li>
            </x-dashboard.layouts.breadcrumb>
            <div class="content-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card role-form-card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="feather icon-plus-circle text-primary"></i>
                                    {{__('dashboard.add roles')}}
                                </h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="form form-vertical" method="POST" action="{{route('admin.roles.store')}}" id="role-form">
                                        @csrf
                                        
                                        <!-- Role titles (ar / en) -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="title-ar" class="form-label">
                                                        <i class="feather icon-tag text-primary"></i>
                                                        {{__('dashboard.role title ar')}} <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                           class="form-control @error('title.ar') is-invalid @enderror"
                                                           name="title[ar]"
                                                           id="title-ar"
                                                           value="{{ old('title.ar') }}"
                                                           placeholder="{{__('dashboard.role title ar')}}"
                                                           required
                                                           dir="rtl">
                                                    @error('title.ar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="title-en" class="form-label">
                                                        <i class="feather icon-tag text-primary"></i>
                                                        {{__('dashboard.role title en')}} <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                           class="form-control @error('title.en') is-invalid @enderror"
                                                           name="title[en]"
                                                           id="title-en"
                                                           value="{{ old('title.en') }}"
                                                           placeholder="{{__('dashboard.role title en')}}"
                                                           required
                                                           dir="ltr">
                                                    @error('title.en')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Permissions Section -->
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="permissions-section">
                                                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                                        <label class="form-label mb-0">
                                                            <i class="feather icon-lock text-primary"></i>
                                                            <strong>{{__('dashboard.Permissions')}}</strong> <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="permission-actions">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" id="select-all-permissions">
                                                                <i class="feather icon-check-square"></i>
                                                                {{__('dashboard.select all')}}
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-secondary ml-1" id="deselect-all-permissions">
                                                                <i class="feather icon-square"></i>
                                                                {{__('dashboard.deselect all')}}
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Search Permissions -->
                                                    <div class="mb-3">
                                                        <input type="text" 
                                                               class="form-control" 
                                                               id="permission-search" 
                                                               placeholder="{{__('dashboard.search permissions')}}...">
                                                    </div>

                                                    <!-- Grouped Permissions -->
                                                    <div class="permissions-container">
                                                        @foreach($groupedPermissions as $category => $categoryPermissions)
                                                            @php
                                                                $groupIcon = match ($category) {
                                                                    'users' => 'users',
                                                                    'admins' => 'shield',
                                                                    'roles' => 'lock',
                                                                    'categories' => 'list',
                                                                    'settings' => 'settings',
                                                                    'preferred_sectors' => 'star',
                                                                    'about_us' => 'info',
                                                                    default => 'folder',
                                                                };
                                                                $categoryLabel = match ($category) {
                                                                    'other' => __('dashboard.other'),
                                                                    'settings' => __('dashboard.settings'),
                                                                    'preferred_sectors' => __('dashboard.preferred_sectors'),
                                                                    'about_us' => __('dashboard.about_us'),
                                                                    default => __('dashboard.' . $category),
                                                                };
                                                            @endphp
                                                            <div class="permission-group mb-3" data-category="{{ $category }}">
                                                                <div class="permission-group-header">
                                                                    <h6 class="mb-0 d-flex justify-content-between align-items-center flex-wrap permission-group-header-inner">
                                                                        <span class="permission-group-title-toggle d-flex align-items-center flex-grow-1">
                                                                            <i class="feather icon-{{ $groupIcon }} text-info"></i>
                                                                            <span class="mr-1 ml-1">{{ $categoryLabel }}</span>
                                                                        </span>
                                                                        <span class="d-flex align-items-center mt-1 mt-sm-0">
                                                                            <span class="group-permission-select-wrap d-inline-flex align-items-center mr-2">
                                                                                <div class="form-check custom-checkbox mb-0">
                                                                                    <input type="checkbox"
                                                                                           class="form-check-input permission-group-select-all"
                                                                                           id="group-select-all-{{ $category }}"
                                                                                           data-category="{{ $category }}"
                                                                                           title="{{ __('dashboard.select all in group') }}">
                                                                                    <label class="form-check-label small mb-0" for="group-select-all-{{ $category }}">{{ __('dashboard.select all in group') }}</label>
                                                                                </div>
                                                                            </span>
                                                                            <span class="badge badge-light-primary permission-count-{{ $category }}">{{ count($categoryPermissions) }}</span>
                                                                        </span>
                                                                    </h6>
                                                                </div>
                                                                <div class="permission-group-body">
                                                                    <div class="row">
                                                                        @foreach($categoryPermissions as $permission)
                                                                            <div class="col-md-4 col-lg-3 col-sm-6 mb-2 permission-item" data-name="{{ strtolower($permission->name) }}">
                                                                                <div class="permission-checkbox-wrapper">
                                                                                    <div class="form-check custom-checkbox">
                                                                                        <input type="checkbox" 
                                                                                               class="form-check-input permission-checkbox" 
                                                                                               name="permission[]" 
                                                                                               id="permission-{{ $permission->id }}" 
                                                                                               value="{{ $permission->id }}">
                                                                                        <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                                                            <span class="permission-text">{{__('roles_permissions.'.$permission->name)}}</span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    @error('permission')
                                                    <div class="alert alert-danger mt-2">
                                                        <i class="feather icon-alert-circle"></i>
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="form-actions d-flex justify-content-end">
                                                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary mr-1">
                                                        <i class="feather icon-x"></i>
                                                        {{__('dashboard.cancel')}}
                                                    </a>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="feather icon-check"></i>
                                                        {{__('dashboard.submit')}}
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
    </div>

    @section('styles')
    <style>
        .role-form-card {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .permissions-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            border: 1px solid #e7e7e7;
        }

        .permission-group {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #e7e7e7;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .permission-group:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .permission-group-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e7e7e7;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .permission-group-header:hover {
            background: linear-gradient(135deg, #f0f0f0 0%, #f8f9fa 100%);
        }

        .permission-group-header h6 {
            font-weight: 600;
            color: #5e5873;
            margin: 0;
        }

        .permission-group-body {
            padding: 1rem;
        }

        .permission-checkbox-wrapper {
            background: #fff;
            border: 1px solid #e7e7e7;
            border-radius: 6px;
            padding: 0.75rem;
            transition: all 0.3s ease;
            height: 100%;
        }

        .permission-checkbox-wrapper:hover {
            border-color: #9C88FF;
            background: #f8f9ff;
            transform: translateY(-2px);
            box-shadow: 0 2px 6px rgba(156, 136, 255, 0.15);
        }

        .custom-checkbox {
            margin: 0;
            width: 100%;
        }

        .custom-checkbox input[type="checkbox"] {
            cursor: pointer;
            width: 18px;
            height: 18px;
            margin-top: 2px;
        }

        .custom-checkbox label {
            cursor: pointer;
            margin-left: 0.5rem;
            width: calc(100% - 30px);
            font-size: 0.9rem;
            color: #5e5873;
            line-height: 1.4;
        }

        .permission-text {
            word-break: break-word;
        }

        .permission-item {
            display: block;
        }

        .permission-item.hidden {
            display: none !important;
        }

        .permission-group.hidden {
            display: none !important;
        }

        .form-label {
            font-weight: 600;
            color: #5e5873;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control {
            border-radius: 6px;
            border: 1px solid #d8d6de;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #9C88FF;
            box-shadow: 0 0 0 0.2rem rgba(156, 136, 255, 0.25);
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
        }

        .permission-actions {
            display: flex;
            gap: 0.5rem;
        }

        #permission-search {
            border-radius: 6px;
            border: 1px solid #d8d6de;
            padding: 0.5rem 1rem;
        }

        #permission-search:focus {
            border-color: #9C88FF;
            box-shadow: 0 0 0 0.2rem rgba(156, 136, 255, 0.25);
        }

        .form-actions {
            padding-top: 1rem;
            border-top: 1px solid #e7e7e7;
        }

        /* Dark Mode */
        body.dark-layout .permissions-section {
            background: #323a5a !important;
            border-color: #414561 !important;
        }

        body.dark-layout .permission-group {
            background: #2b3553 !important;
            border-color: #414561 !important;
        }

        body.dark-layout .permission-group-header {
            background: linear-gradient(135deg, #323a5a 0%, #2b3553 100%) !important;
            border-bottom-color: #414561 !important;
        }

        body.dark-layout .permission-group-header h6 {
            color: #ebeefd !important;
        }

        body.dark-layout .permission-checkbox-wrapper {
            background: #2b3553 !important;
            border-color: #414561 !important;
        }

        body.dark-layout .permission-checkbox-wrapper:hover {
            background: #323a5a !important;
            border-color: #9C88FF !important;
        }

        body.dark-layout .custom-checkbox label {
            color: #c2c6dc !important;
        }

        body.dark-layout .form-label {
            color: #ebeefd !important;
        }

        body.dark-layout .form-control {
            background: #323a5a !important;
            border-color: #414561 !important;
            color: #ebeefd !important;
        }

        body.dark-layout .form-actions {
            border-top-color: #414561 !important;
        }

        @media (max-width: 768px) {
            .permission-group-body .row {
                margin: 0;
            }

            .permission-item {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .permission-actions {
                flex-direction: column;
                width: 100%;
                margin-top: 1rem;
            }

            .permission-actions .btn {
                width: 100%;
                margin-left: 0 !important;
            }
        }
    </style>
    @endsection

    @section('script')
    <script>
        $(document).ready(function() {
            function updateGroupSelectAllState($group) {
                var $boxes = $group.find('.permission-checkbox');
                var total = $boxes.length;
                var checked = $boxes.filter(':checked').length;
                var $master = $group.find('.permission-group-select-all');
                $master.prop('indeterminate', false);
                if (total === 0) {
                    $master.prop('checked', false);
                    return;
                }
                if (checked === 0) {
                    $master.prop('checked', false);
                } else if (checked === total) {
                    $master.prop('checked', true);
                } else {
                    $master.prop('checked', false);
                    $master.prop('indeterminate', true);
                }
            }

            function updateAllGroupSelectAllStates() {
                $('.permission-group').each(function () {
                    updateGroupSelectAllState($(this));
                });
            }

            // Permission Search
            $('#permission-search').on('keyup', function() {
                var searchTerm = $(this).val().toLowerCase();

                if (searchTerm === '') {
                    $('.permission-item').removeClass('hidden').show();
                    $('.permission-group').removeClass('hidden').show();
                    updatePermissionCounts();
                    updateAllGroupSelectAllStates();
                    return;
                }

                $('.permission-item').each(function() {
                    var permissionName = $(this).data('name');
                    var permissionText = $(this).find('.permission-text').text().toLowerCase();

                    if (permissionName.includes(searchTerm) || permissionText.includes(searchTerm)) {
                        $(this).removeClass('hidden').show();
                    } else {
                        $(this).addClass('hidden').hide();
                    }
                });

                $('.permission-group').each(function() {
                    var visibleItems = $(this).find('.permission-item:not(.hidden):visible').length;
                    if (visibleItems === 0) {
                        $(this).addClass('hidden').hide();
                    } else {
                        $(this).removeClass('hidden').show();
                    }
                });

                updatePermissionCounts();
                updateAllGroupSelectAllStates();
            });

            $('#select-all-permissions').on('click', function() {
                $('.permission-checkbox:visible:not(.hidden)').prop('checked', true);
                updatePermissionCounts();
                updateAllGroupSelectAllStates();
            });

            $('#deselect-all-permissions').on('click', function() {
                $('.permission-checkbox:visible:not(.hidden)').prop('checked', false);
                updatePermissionCounts();
                updateAllGroupSelectAllStates();
            });

            $(document).on('change', '.permission-group-select-all', function() {
                var $group = $(this).closest('.permission-group');
                var on = $(this).prop('checked');
                $(this).prop('indeterminate', false);
                $group.find('.permission-checkbox').prop('checked', on);
                updatePermissionCounts();
                updateGroupSelectAllState($group);
            });

            $('.permission-group-title-toggle').on('click', function() {
                $(this).closest('.permission-group').find('.permission-group-body').slideToggle(300);
            });

            $('.permission-checkbox').on('change', function() {
                updatePermissionCounts();
                updateGroupSelectAllState($(this).closest('.permission-group'));
            });

            function updatePermissionCounts() {
                $('.permission-group:not(.hidden)').each(function() {
                    var category = $(this).data('category');
                    var checkedCount = $(this).find('.permission-checkbox:checked:visible:not(.hidden)').length;
                    var totalCount = $(this).find('.permission-checkbox:visible:not(.hidden)').length;
                    if (totalCount > 0) {
                        $('.permission-count-' + category).text(checkedCount + '/' + totalCount);
                    }
                });
            }

            $('#role-form').on('submit', function(e) {
                var checkedPermissions = $('.permission-checkbox:checked').length;
                if (checkedPermissions === 0) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            position: 'top-start',
                            icon: 'warning',
                            title: @json(__('dashboard.validation_errors_title')),
                            text: @json(__('dashboard.please select at least one permission')),
                            confirmButtonText: @json(__('dashboard.confirm'))
                        });
                    } else {
                        alert(@json(__('dashboard.please select at least one permission')));
                    }
                    return false;
                }
            });

            updatePermissionCounts();
            updateAllGroupSelectAllStates();
        });
    </script>
    @endsection
</x-dashboard.layouts.master>
