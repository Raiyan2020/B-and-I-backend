<script>
    $(document).ready(function() {
        const canEdit = @json(auth('admin')->user()?->can('edit-subscription-package'));
        const canDelete = @json(auth('admin')->user()?->can('delete-subscription-package'));
        var settingsPanelSelector = '#subscription-packages-settings-panel';

        function initPackagesPageEditorsOnce() {
            if (typeof CKEDITOR === 'undefined') {
                return;
            }
            if (!CKEDITOR.instances['packages_page_description_ar']) {
                CKEDITOR.replace('packages_page_description_ar', {
                    language: 'ar',
                    contentsLangDirection: 'rtl',
                    height: 280,
                    versionCheck: false
                });
            }
            if (!CKEDITOR.instances['packages_page_description_en']) {
                CKEDITOR.replace('packages_page_description_en', {
                    language: 'en',
                    contentsLangDirection: 'ltr',
                    height: 280,
                    versionCheck: false
                });
            }
        }

        $(settingsPanelSelector).on('shown.bs.collapse', function() {
            initPackagesPageEditorsOnce();
        });
        if ($(settingsPanelSelector).hasClass('show')) {
            initPackagesPageEditorsOnce();
        }

        var table = $('#subscription-packages-table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            lengthMenu: [5, 10, 20, 40, 60, 80, 100],
            pageLength: 10,
            ajax: {
                url: "{{ route('admin.subscription_packages.index') }}",
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.filters = {
                        name: $('#search-text').val() || '',
                    };
                    var st = $('#status-filter').val();
                    if (st !== '') {
                        d.filters['status__eq'] = st;
                    }
                }
            },
            paging: true,
            ordering: false,
            columns: [{
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return `<input type="checkbox" class="dt-select-row" data-id="${data}" title="{{ __('dashboard.select') }}">`;
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
                    data: 'price_monthly',
                    name: 'price_monthly',
                    render: function(data) {
                        return data != null ? Number(data).toLocaleString() : '-';
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
                        var tmp = document.createElement('div');
                        tmp.innerHTML = text;
                        text = tmp.textContent || tmp.innerText || '';
                        if (text.length > 80) {
                            return text.substring(0, 80) + '...';
                        }
                        return text || '-';
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data) {
                        return `<span class="badge text-white badge-${data == 1 ? 'success' : 'danger'}">${data == 1 ? '{{ __('dashboard.active') }}' : '{{ __('dashboard.in-active') }}'}</span>`;
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function(data) {
                        if (!data) return '-';
                        var date = new Date(data);
                        var y = date.getFullYear();
                        var m = String(date.getMonth() + 1).padStart(2, '0');
                        var day = String(date.getDate()).padStart(2, '0');
                        var h = String(date.getHours()).padStart(2, '0');
                        var min = String(date.getMinutes()).padStart(2, '0');
                        return `${y}-${m}-${day} ${h}:${min}`;
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    render: function(data, type, row) {
                        let actions = [];
                        let editRoute = '{{ route('admin.subscription_packages.edit', ':id') }}'.replace(':id', data);
                        let deleteRoute = '{{ route('admin.subscription_packages.destroy', ':id') }}'.replace(':id', data);
                        let toggleRoute = '{{ route('admin.subscription_packages.toggleStatus', ':id') }}'.replace(':id', data);
                        let statusTitle = row.status == 1 ?
                            '{{ __('dashboard.deactivate') }}' : '{{ __('dashboard.activate') }}';
                        if (canEdit) {
                            actions.push(`<a class="btn btn-sm btn-icon btn-outline-primary" href="${editRoute}" title="{{ __('dashboard.edit') }}">
                                <i class="feather icon-edit text-primary"></i>
                            </a>`);
                            actions.push(`<button type="button" class="btn btn-sm btn-icon btn-outline-${row.status == 1 ? 'warning' : 'success'} toggle-status-btn" data-url="${toggleRoute}" data-status="${row.status}" title="${statusTitle}">
                                <i class="feather icon-${row.status == 1 ? 'x-circle' : 'check-circle'} text-${row.status == 1 ? 'warning' : 'success'}"></i>
                            </button>`);
                        }
                        if (canDelete) {
                            actions.push(`<button type="button" class="btn btn-sm btn-icon btn-outline-danger delete-row" data-url="${deleteRoute}" title="{{ __('dashboard.delete') }}">
                                <i class="feather icon-trash-2 text-danger"></i>
                            </button>`);
                        }
                        return `<div class="d-flex align-items-center gap-2">${actions.join('')}</div>`;
                    }
                }
            ]
        });

        $('#filter-btn').on('click', function() {
            table.ajax.reload(null, false);
        });
        $('#reset-filter-btn').on('click', function() {
            $('#search-text').val('');
            $('#status-filter').val('');
            table.ajax.reload(null, false);
        });
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            $('#filter-btn').click();
        });

        table.one('init.dt', function() {
            if (window.TableSelection) {
                window.TableSelection.init('#subscription-packages-table');
            }
        });
        if (window.TableSelection) {
            window.TableSelection.init('#subscription-packages-table');
        }
        if (window.BulkDelete) {
            window.BulkDelete.init({
                tableSelector: '#subscription-packages-table',
                deleteUrl: '{{ route('admin.subscription_packages.destroyMultiple') }}',
                csrfToken: $('meta[name="csrf-token"]').attr('content')
            });
        }

        $(document).on('click', '.toggle-status-btn', function(e) {
            e.preventDefault();
            let btn = $(this);
            let url = btn.data('url');
            let currentStatus = btn.data('status');
            Swal.fire({
                title: '{{ __('dashboard.confirm') }}',
                text: currentStatus == 1 ?
                    '{{ __('dashboard.deactivate_package_confirm') }}' :
                    '{{ __('dashboard.activate_package_confirm') }}',
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

        $('#save-settings-btn').on('click', function() {
            if (!canEdit) {
                return;
            }
            if (typeof CKEDITOR !== 'undefined') {
                ['packages_page_description_ar', 'packages_page_description_en'].forEach(function(id) {
                    if (CKEDITOR.instances[id]) {
                        CKEDITOR.instances[id].updateElement();
                    }
                });
            }
            var btn = $(this);
            var originalText = btn.html();
            $.ajax({
                url: '{{ route('admin.subscription_packages.updateSettings') }}',
                type: 'POST',
                data: $('#settings-form').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    btn.html('<span class="spinner-border spinner-border-sm"></span>').attr('disabled', true);
                },
                success: function(response) {
                    btn.html(originalText).attr('disabled', false);
                    Swal.fire({
                        position: 'top-start',
                        icon: 'success',
                        title: response.msg || '{{ __('dashboard.item updated successfully') }}',
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(xhr) {
                    btn.html(originalText).attr('disabled', false);
                    var errorMsg = xhr.responseJSON?.message || '{{ __('dashboard.something_went_wrong') }}';
                    if (xhr.responseJSON?.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: errorMsg,
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        });
    });
</script>
