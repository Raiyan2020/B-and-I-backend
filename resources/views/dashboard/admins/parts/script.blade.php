<script>
    $(document).ready(function() {
        var table = $('#admins-table').DataTable({

            processing: true,
            serverSide: true,
            searching: false,
            lengthMenu: [5,10, 20, 40, 60, 80, 100],
            pageLength: 5,
            ajax: {
                url: "{{ route('admin.admins.index') }}",
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    // Add filter parameters (only when filter button is clicked)
                    d.filters = {
                        name: $('#search-name').val() || '',
                        phone: $('#search-phone').val() || '',
                        'roles.name': $('#role-filter').val() || '',
                        order: window.DataTablesShared
                            ? window.DataTablesShared.getOrderFilterValue()
                            : ($('#order-filter').val() || 'DESC'),
                        is_blocked: $('#block-status-filter').val() || ''
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
                            return window.TableHelpers.renderImage(data, 'feather icon-shield',
                                'Admin Image');
                        }
                        // Fallback
                        if (data && data !== null && data !== '' && data !== undefined) {
                            return `<img class="table-image" src="${data}" alt="Admin Image" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'table-image-placeholder\\'><i class=\\'feather icon-shield\\'></i></div>';" />`;
                        }
                        return `<div class="table-image-placeholder"><i class="feather icon-shield"></i></div>`;
                    }
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'phone',
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
                    data: 'roles',
                    render: function(data) {
                        if (!data || data.length === 0) {
                            return '{{ __('dashboard.no role') }}';
                        }
                        var r = data[0];
                        return r.display_name || r.name;
                    }
                },
                {
                    data: 'is_blocked',
                    name: 'is_blocked',
                    render: function(data) {
                        return `<span class="badge text-white badge-${data == 1 ? 'danger' : 'success'}">${data == 1 ? '{{ __('dashboard.blocked') }}' : '{{ __('dashboard.un_blocked') }}'}</span>`
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    render: function(data, type, row) {
                        if (data == 1) return '';

                        let editRoute = '{{ route('admin.admins.edit', ':id') }}'.replace(':id',
                            data);
                        let showRoute = '{{ route('admin.admins.show', ':id') }}'.replace(':id',
                            data);
                        let deleteRoute = '{{ route('admin.admins.destroy', ':id') }}'.replace(
                            ':id', data);
                        let toggleBlockRoute = '{{ route('admin.admins.toggleBlock', ':id') }}'
                            .replace(':id', data);

                        if (window.DataTablesShared && window.DataTablesShared.renderActions) {
                            return window.DataTablesShared.renderActions({
                                showRoute: showRoute,
                                editRoute: editRoute,
                                deleteRoute: deleteRoute,
                                toggleBlockRoute: toggleBlockRoute,
                                hideIf: function(id) {
                                    return id == 1;
                                }
                            })(data, type, row);
                        }

                        // Fallback
                        let blockTitle = row.is_blocked == 1 ?
                            '{{ __('dashboard.un_block') }}' : '{{ __('dashboard.block') }}';
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
            $('#role-filter').val('');
            $('#block-status-filter').val('');
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
                window.TableSelection.init('#admins-table');
            }
        });

        // Also initialize immediately (in case init event already fired)
        if (window.TableSelection) {
            window.TableSelection.init('#admins-table');
        }

        // Initialize bulk delete
        if (window.BulkDelete) {
            window.BulkDelete.init({
                tableSelector: '#admins-table',
                deleteUrl: '{{ route('admin.admins.destroyMultiple') }}',
                csrfToken: $('meta[name="csrf-token"]').attr('content')
            });
        }

        // Initialize toggle block
        if (window.ToggleBlock) {
            window.ToggleBlock.init({
                tableSelector: '#admins-table',
                csrfToken: $('meta[name="csrf-token"]').attr('content')
            });
        }
    });
</script>
