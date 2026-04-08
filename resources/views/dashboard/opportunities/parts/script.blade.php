<script>
    $(document).ready(function() {
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
                            pending_review: 'warning',
                            approved: 'success',
                            needs_modification: 'danger'
                        };
                        const labels = {
                            pending_review: '{{ __('dashboard.opportunity_status_pending_review') }}',
                            approved: '{{ __('dashboard.opportunity_status_approved') }}',
                            needs_modification: '{{ __('dashboard.opportunity_status_needs_modification') }}'
                        };
                        return `<span class="badge badge-${map[data] || 'secondary'}">${labels[data] || data}</span>`;
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
