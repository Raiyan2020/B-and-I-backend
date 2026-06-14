<script>
    $(document).ready(function() {
        var table = $('#users-table').DataTable({

            processing: true,
            serverSide: true,
            searching: false,
            lengthMenu: [5,10, 20, 40, 60, 80, 100],
            pageLength: 5,
            ajax: {
                url: "{{ route($indexRouteName ?? 'admin.users.index') }}",
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    // Add filter parameters (only when filter button is clicked)
                    d.filters = {
                        name: $('#search-name').val() || '',
                        phone: $('#search-phone').val() || '',
                        email: $('#search-email').val() || '',
                        order: window.DataTablesShared
                            ? window.DataTablesShared.getOrderFilterValue()
                            : ($('#order-filter').val() || 'DESC'),
                        is_blocked: $('#block-status-filter').val() || '',
                        is_active: $('#account-status-filter').val() || ''
                    };
                }
            },
            "paging": true,
            ordering: false,
            columns: [{
                    // Checkbox column (rendered by DataTables with row ID)
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return `<input type="checkbox" class="dt-select-row" data-id="${data}" title="{{ __('dashboard.select') }}">`;
                    }
                },
                {
                    data: 'image',
                    render: function(data) {
                        if (window.TableHelpers) {
                            return window.TableHelpers.renderImage(data, 'feather icon-user',
                                'User Image');
                        }
                        // Fallback
                        if (data && data !== null && data !== '' && data !== undefined) {
                            return `<img class="table-image" src="${data}" alt="User Image" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'table-image-placeholder\\'><i class=\\'feather icon-user\\'></i></div>';" />`;
                        }
                        return `<div class="table-image-placeholder"><i class="feather icon-user"></i></div>`;
                    }
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'full_phone',
                    name: 'phone',
                    render: function(data, type) {
                        if (type !== 'display') {
                            return data;
                        }

                        return window.TableHelpers ? window.TableHelpers.renderPhone(data) : data;
                    }
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'is_blocked',
                    name: 'is_blocked',
                    render: function(data) {
                        return `<span class="badge text-white badge-${data == 1 ? 'danger' : 'success'}">${data == 1 ? '{{ __('dashboard.blocked') }}' : '{{ __('dashboard.un_blocked') }}'}</span>`
                    }
                },
                {
                    data: 'is_active',
                    name: 'is_active',
                    render: function(data) {
                        return `<span class="badge text-white badge-${data ? 'success' : 'warning'}">${data ? '{{ __('dashboard.active') }}' : '{{ __('dashboard.inactive') }}'}</span>`
                    }
                },
                {
                    data: 'latest_pending_profile_update_request',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        if (!data || !data.id) {
                            return `-`;
                        }

                        let reviewRoute = '{{ route('admin.profile-update-requests.show', ':id') }}'
                            .replace(':id', data.id);

                        return `<a href="${reviewRoute}" class="btn btn-sm btn-outline-info">
                                    <i class="feather icon-eye mr-50"></i>{{ __('dashboard.review_request') }}
                                </a>`;
                    }
                },
                {
                    data: 'latest_pending_account_deletion_request',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        if (!data || !data.id) {
                            return `-`;
                        }

                        let reviewRoute = '{{ route('admin.account-deletion-requests.show', ':id') }}'
                            .replace(':id', data.id);

                        return `<a href="${reviewRoute}" class="btn btn-sm btn-outline-danger">
                                    <i class="feather icon-trash-2 mr-50"></i>{{ __('dashboard.show') }}
                                </a>`;
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    render: function(data, type, row) {
                        let editRoute = '{{ route('admin.users.edit', ':id') }}'.replace(':id',
                            data);
                        let showRoute = '{{ route('admin.users.show', ':id') }}'.replace(':id',
                            data);
                        let deleteRoute = '{{ route('admin.users.destroy', ':id') }}'.replace(
                            ':id', data);
                        let toggleBlockRoute = '{{ route('admin.users.toggleBlock', ':id') }}'
                            .replace(':id', data);
                        let toggleActiveRoute = '{{ route('admin.users.toggleActive', ':id') }}'
                            .replace(':id', data);

                        let blockTitle = row.is_blocked == 1 ?
                            '{{ __('dashboard.un_block') }}' : '{{ __('dashboard.block') }}';
                        let isActive = !!row.is_active;
                        let activeTitle = isActive ?
                            '{{ __('dashboard.deactivate') }}' : '{{ __('dashboard.activate') }}';
                        return `<div class="d-flex align-items-center gap-2">
                                    <a class="btn btn-sm btn-icon btn-outline-info" href="${showRoute}" title="{{ __('dashboard.show') }}">
                                        <i class="feather icon-eye text-info"></i>
                                    </a>
                                    <a class="btn btn-sm btn-icon btn-outline-primary" href="${editRoute}" title="{{ __('dashboard.edit') }}">
                                        <i class="feather icon-edit text-primary"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-warning toggle-block-btn" data-url="${toggleBlockRoute}" data-blocked="${row.is_blocked}" title="${blockTitle}">
                                        <i class="feather icon-slash text-warning"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-${isActive ? 'success' : 'secondary'} toggle-active-btn" data-url="${toggleActiveRoute}" data-active="${isActive ? 1 : 0}" title="${activeTitle}">
                                        <i class="feather icon-${isActive ? 'check-circle' : 'x-circle'} text-${isActive ? 'success' : 'secondary'}"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger delete-row" data-url="${deleteRoute}" title="{{ __('dashboard.delete') }}">
                                        <i class="feather icon-trash-2 text-danger"></i>
                                    </button>
                                </div>`;
                    }
                },
            ]
        });

        // Filter button click
        $('#filter-btn').on('click', function() {
            table.ajax.reload(null, false);
        });

        // Reset filter button (Refresh icon)
        $('#reset-filter-btn').on('click', function() {
            // Reset all filter fields
            $('#search-name').val('');
            $('#search-phone').val('');
            $('#search-email').val('');
            $('#block-status-filter').val('');
            $('#account-status-filter').val('');
            $('#order-filter').val('DESC');
            // Reload table with reset filters
            table.ajax.reload(null, false);
        });

        // Prevent form submission on Enter key
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            $('#filter-btn').click();
        });

        // Initialize table selection after DataTable is fully initialized
        table.one('init.dt', function() {
            if (window.TableSelection) {
                window.TableSelection.init('#users-table');
            }
        });

        // Also initialize immediately (in case init event already fired)
        if (window.TableSelection) {
            window.TableSelection.init('#users-table');
        }

        // Initialize bulk delete
        if (window.BulkDelete) {
            window.BulkDelete.init({
                tableSelector: '#users-table',
                deleteUrl: '{{ route('admin.users.destroyMultiple') }}',
                csrfToken: $('meta[name="csrf-token"]').attr('content')
            });
        }

        // Initialize toggle block
        if (window.ToggleBlock) {
            window.ToggleBlock.init({
                tableSelector: '#users-table',
                csrfToken: $('meta[name="csrf-token"]').attr('content')
            });
        }

        // Initialize toggle active (account status)
        $(document).on('click', '.toggle-active-btn', function(e) {
            e.preventDefault();
            let btn = $(this);
            let url = btn.data('url');

            Swal.fire({
                title: '{{ __('dashboard.confirm') }}',
                text: '{{ __('dashboard.toggle_active_text') }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __('dashboard.yes') }}',
                cancelButtonText: '{{ __('dashboard.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.key === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '{{ __('dashboard.success') }}',
                                    text: response.msg,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('dashboard.error') }}',
                                    text: response.msg
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('dashboard.error') }}',
                                text: xhr.responseJSON?.msg || '{{ __('dashboard.something_went_wrong') }}'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
