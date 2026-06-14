<script>
    $(document).ready(function() {
        var table = $('#about-us-items-table').DataTable({

            processing: true,
            serverSide: true,
            searching: false,
            lengthMenu: [5, 10, 20, 40, 60, 80, 100],
            pageLength: 10,
            ajax: {
                url: "{{ route('admin.about_us_items.index') }}",
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.filters = {
                        title: $('#search-text').val() || '',
                        status: $('#status-filter').val() || '',
                    };
                }
            },
            "paging": true,
            order: [[5, 'desc']],
            columns: [{
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
                                'About Us Image');
                        }
                        if (data && data !== null && data !== '' && data !== undefined) {
                            return `<img class="table-image" src="${data}" alt="About Us Image" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'table-image-placeholder\\'><i class=\\'feather icon-image\\'></i></div>';" />`;
                        }
                        return `<div class="table-image-placeholder"><i class="feather icon-image"></i></div>`;
                    }
                },
                {
                    data: 'title',
                    name: 'title',
                    render: function(data) {
                        if (data && typeof data === 'object') {
                            return data['{{ app()->getLocale() }}'] || data['en'] || data['ar'] || '';
                        }
                        return data || '';
                    }
                },
                {
                    data: 'description',
                    name: 'description',
                    render: function(data) {
                        var text = '';
                        if (data && typeof data === 'object') {
                            text = data['{{ app()->getLocale() }}'] || data['en'] || data['ar'] || '';
                        } else {
                            text = data || '';
                        }
                        // Truncate description
                        if (text.length > 80) {
                            return text.substring(0, 80) + '...';
                        }
                        return text;
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
                        let editRoute = '{{ route('admin.about_us_items.edit', ':id') }}'.replace(':id',
                            data);
                        let deleteRoute = '{{ route('admin.about_us_items.destroy', ':id') }}'.replace(
                            ':id', data);
                        let toggleStatusRoute = '{{ route('admin.about_us_items.toggleStatus', ':id') }}'
                            .replace(':id', data);

                        if (window.DataTablesShared && window.DataTablesShared.renderActions) {
                            return window.DataTablesShared.renderActions({
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

        // Reset filter button
        $('#reset-filter-btn').on('click', function() {
            $('#search-text').val('');
            $('#status-filter').val('');
            table.ajax.reload(null, false);
        });

        // Prevent form submission on Enter key
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            $('#filter-btn').click();
        });

        // Initialize table selection
        table.one('init.dt', function() {
            if (window.TableSelection) {
                window.TableSelection.init('#about-us-items-table');
            }
        });

        if (window.TableSelection) {
            window.TableSelection.init('#about-us-items-table');
        }

        // Initialize bulk delete
        if (window.BulkDelete) {
            window.BulkDelete.init({
                tableSelector: '#about-us-items-table',
                deleteUrl: '{{ route('admin.about_us_items.destroyMultiple') }}',
                csrfToken: $('meta[name="csrf-token"]').attr('content')
            });
        }

        // Toggle status handler
        $(document).on('click', '.toggle-status-btn', function(e) {
            e.preventDefault();
            let btn = $(this);
            let url = btn.data('url');
            let currentStatus = btn.data('status');

            Swal.fire({
                title: '{{ __('dashboard.confirm') }}',
                text: currentStatus == 1 ? '{{ __('dashboard.deactivate_about_us_confirm') }}' : '{{ __('dashboard.activate_about_us_confirm') }}',
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

        // Save Settings AJAX handler
        $('#save-settings-btn').on('click', function() {
            var btn = $(this);
            var originalText = btn.html();
            var form = $('#settings-form');

            $.ajax({
                url: '{{ route('admin.about_us_items.updateSettings') }}',
                type: 'POST',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').attr('disabled', true);
                },
                success: function(response) {
                    btn.html(originalText).attr('disabled', false);
                    Swal.fire({
                        position: "top-start",
                        icon: "success",
                        title: response.msg || '{{ __('dashboard.item updated successfully') }}',
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(xhr) {
                    btn.html(originalText).attr('disabled', false);
                    var errorMsg = xhr.responseJSON?.message || '{{ __('dashboard.something_went_wrong') }}';
                    if (xhr.responseJSON?.errors) {
                        var errors = xhr.responseJSON.errors;
                        errorMsg = Object.values(errors).flat().join('\n');
                    }
                    Swal.fire({
                        position: "top-start",
                        icon: "error",
                        title: errorMsg,
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        });
    });
</script>
