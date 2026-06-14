<script>
    $(document).ready(function() {
        const canShow = @json(auth('admin')->user()?->can('show-interest-request'));
        const opportunityShowRouteTemplate = '{{ route('admin.opportunities.show', ':id') }}';
        const opportunityId = @json($opportunityId);

        $('#interest-requests-table').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            ajax: {
                url: "{{ route('admin.interest-requests.index') }}",
                data: function(d) {
                    if (opportunityId) {
                        d.opportunity_id = opportunityId;
                    }
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {
                    data: 'opportunity_name',
                    name: 'opportunity.company_name',
                    defaultContent: '-',
                    render: function(data, type, row) {
                        if (type !== 'display' || !row.opportunity_id || data === '-') {
                            return data ?? '-';
                        }

                        const showRoute = opportunityShowRouteTemplate.replace(':id', row.opportunity_id);

                        return `<a href="${showRoute}" class="text-primary fw-semibold">${data}</a>`;
                    }
                },
                {data: 'advertiser_name', name: 'opportunity.user.first_name', defaultContent: '-'},
                {data: 'investor_name', name: 'user.first_name', defaultContent: '-'},
                {data: 'seat_reference', name: 'investment_seat_id'},
                {data: 'created_at', name: 'created_at'},
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        if (!canShow) {
                            return '';
                        }
                        let showRoute = '{{ route('admin.interest-requests.show', ':id') }}'.replace(':id', data);
                        return `<a class="btn btn-sm btn-outline-primary" href="${showRoute}"><i class="feather icon-eye"></i></a>`;
                    }
                }
            ]
        });
    });
</script>
