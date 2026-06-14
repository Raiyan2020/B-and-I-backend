<script>
    $(document).ready(function() {
        var table = $('#categories-table').DataTable({

            processing: true,
            serverSide: true,
            searching: false,
            lengthMenu: [5,10, 20, 40, 60, 80, 100],
            pageLength: 5,
            ajax: {
                url: "{{ route('admin.categories.index') }}",
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    // Add filter parameters (only when filter button is clicked)
                    d.filters = {
                        name: $('#search-name').val() || '',
                        status: $('#status-filter').val() || '',
                        order: $('#order-filter').val() || 'ASC',
                    };
                }
            },
            "paging": true,
            order: [[3, 'asc']], // Sort by order column (index 3) ascending by default
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
                            return window.TableHelpers.renderImage(data, 'feather icon-image',
                                'Category Image');
                        }
                        // Fallback
                        if (data && data !== null && data !== '' && data !== undefined) {
                            return `<img class="table-image" src="${data}" alt="Category Image" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'table-image-placeholder\\'><i class=\\'feather icon-image\\'></i></div>';" />`;
                        }
                        return `<div class="table-image-placeholder"><i class="feather icon-image"></i></div>`;
                    }
                },
                {
                    data: 'name',
                    name: 'name',
                    render: function(data) {
                        if (data && typeof data === 'object') {
                            return data['{{ app()->getLocale() }}'] || data['en'] || data['ar'] || '';
                        }
                        return data || '';
                    }
                },
                {
                    data: 'order',
                    name: 'order',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data) {
                        return `<span class="badge text-white badge-${data == 1 ? 'success' : 'danger'}">${data == 1 ? '{{ __('dashboard.active') }}' : '{{ __('dashboard.in-active') }}'}</span>`
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function(data) {
                        if (!data) return '-';
                        // Format date: Y-m-d H:i
                        var date = new Date(data);
                        var year = date.getFullYear();
                        var month = String(date.getMonth() + 1).padStart(2, '0');
                        var day = String(date.getDate()).padStart(2, '0');
                        var hours = String(date.getHours()).padStart(2, '0');
                        var minutes = String(date.getMinutes()).padStart(2, '0');
                        return `${year}-${month}-${day} ${hours}:${minutes}`;
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    render: function(data, type, row) {
                        let editRoute = '{{ route('admin.categories.edit', ':id') }}'.replace(':id',
                            data);
                        let showRoute = '{{ route('admin.categories.show', ':id') }}'.replace(':id',
                            data);
                        let deleteRoute = '{{ route('admin.categories.destroy', ':id') }}'.replace(
                            ':id', data);
                        let toggleStatusRoute = '{{ route('admin.categories.toggleStatus', ':id') }}'
                            .replace(':id', data);

                        if (window.DataTablesShared && window.DataTablesShared.renderActions) {
                            return window.DataTablesShared.renderActions({
                                showRoute: showRoute,
                                editRoute: editRoute,
                                deleteRoute: deleteRoute,
                                toggleStatusRoute: toggleStatusRoute,
                                hideIf: function(id) {
                                    return false;
                                }
                            })(data, type, row);
                        }

                        // Fallback
                        let statusTitle = row.status == 1 ?
                            '{{ __('dashboard.deactivate') }}' : '{{ __('dashboard.activate') }}';
                        return `<div class="d-flex align-items-center gap-2">
                                    <a class="btn btn-sm btn-icon btn-outline-info" href="${showRoute}" title="{{ __('dashboard.show') }}">
                                        <i class="feather icon-eye text-info"></i>
                                    </a>
                                    <a class="btn btn-sm btn-icon btn-outline-primary" href="${editRoute}" title="{{ __('dashboard.edit') }}">
                                        <i class="feather icon-edit text-primary"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-${row.status == 1 ? 'warning' : 'success'} toggle-status-btn" data-url="${toggleStatusRoute}" data-status="${row.status}" title="${statusTitle}">
                                        <i class="feather icon-${row.status == 1 ? 'x-circle' : 'check-circle'} text-${row.status == 1 ? 'warning' : 'success'}"></i>
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
            $('#status-filter').val('');
            $('#order-filter').val('ASC');
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
                window.TableSelection.init('#categories-table');
            }
        });

        // Also initialize immediately (in case init event already fired)
        if (window.TableSelection) {
            window.TableSelection.init('#categories-table');
        }

        // Initialize bulk delete
        if (window.BulkDelete) {
            window.BulkDelete.init({
                tableSelector: '#categories-table',
                deleteUrl: '{{ route('admin.categories.destroyMultiple') }}',
                csrfToken: $('meta[name="csrf-token"]').attr('content')
            });
        }

        // Initialize toggle status
        $(document).on('click', '.toggle-status-btn', function(e) {
            e.preventDefault();
            let btn = $(this);
            let url = btn.data('url');
            let currentStatus = btn.data('status');
            
            Swal.fire({
                title: '{{ __('dashboard.confirm') }}',
                text: currentStatus == 1 ? '{{ __('dashboard.deactivate_category_confirm') }}' : '{{ __('dashboard.activate_category_confirm') }}',
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
