<script>
    $(document).ready(function() {
        const investmentSeatsRouteTemplate = '{{ route('admin.investment-seats.index') }}';
        const interestRequestsRouteTemplate = '{{ route('admin.interest-requests.index') }}';

        let table = $('#opportunities-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.opportunities.index') }}",
                data: function(d) {
                    d.filters = {
                        status: $('#status-filter').val() || '',
                        goal: $('#goal-filter').val() || '',
                        company_name: $('#search-company').val() || '',
                    };
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'company_name', name: 'company_name'},
                {
                    data: 'goal',
                    render: function(data) {
                        const map = {
                            sell_business: '{{ __('dashboard.goal_sell_business') }}',
                            request_investment: '{{ __('dashboard.goal_request_investment') }}'
                        };
                        return map[data] || data || '-';
                    }
                },
                {
                    data: 'category',
                    render: function(data) {
                        if (!data) return '-';
                        if (typeof data.name === 'string') return data.name;
                        return data.name?.['{{ app()->getLocale() }}'] || data.name?.ar || data.name?.en || '-';
                    }
                },
                {
                    data: 'status',
                    render: function(data) {
                        const map = {
                            pending: 'warning',
                            needs_revision: 'danger',
                            published: 'success',
                            reserved: 'info',
                            completed: 'secondary'
                        };
                        const labels = {
                            pending: '{{ __('dashboard.opportunity_status_pending') }}',
                            needs_revision: '{{ __('dashboard.opportunity_status_needs_revision') }}',
                            published: '{{ __('dashboard.opportunity_status_published') }}',
                            reserved: '{{ __('dashboard.opportunity_status_reserved') }}',
                            completed: '{{ __('dashboard.opportunity_status_completed') }}'
                        };
                        return `<span class="badge badge-${map[data] || 'secondary'}">${labels[data] || data}</span>`;
                    }
                },
                {
                    data: 'investment_seats_count',
                    name: 'investment_seats_count',
                    searchable: false,
                    render: function(data, type, row) {
                        if (type !== 'display') {
                            return data;
                        }

                        const url = `${investmentSeatsRouteTemplate}?opportunity_id=${row.id}`;
                        return `<a href="${url}" class="badge badge-light-primary">${data ?? 0}</a>`;
                    }
                },
                {
                    data: 'interest_requests_count',
                    name: 'interest_requests_count',
                    searchable: false,
                    render: function(data, type, row) {
                        if (type !== 'display') {
                            return data;
                        }

                        const url = `${interestRequestsRouteTemplate}?opportunity_id=${row.id}`;
                        return `<a href="${url}" class="badge badge-light-info">${data ?? 0}</a>`;
                    }
                },
                {data: 'created_at', name: 'created_at'},
                {
                    data: 'id',
                    orderable: false,
                    render: function(data) {
                        let showRoute = '{{ route('admin.opportunities.show', ':id') }}'.replace(':id', data);
                        return `<a class="btn btn-sm btn-outline-primary" href="${showRoute}"><i class="feather icon-eye"></i></a>`;
                    }
                }
            ]
        });

        $('#filter-btn').on('click', function() { table.ajax.reload(null, false); });
        $('#reset-filter-btn').on('click', function() {
            $('#status-filter').val('');
            $('#goal-filter').val('');
            $('#search-company').val('');
            table.ajax.reload(null, false);
        });
    });
</script>
