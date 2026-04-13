<script>
    $(document).ready(function() {
        const opportunityShowRouteTemplate = '{{ route('admin.opportunities.show', ':id') }}';
        const opportunityId = @json($opportunityId);

        $('#investment-seats-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.investment-seats.index') }}",
                data: function(d) {
                    if (opportunityId) {
                        d.opportunity_id = opportunityId;
                    }
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {
                    data: 'opportunity_reference',
                    name: 'opportunity.opportunity_number',
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
                {data: 'price_paid', name: 'price_paid'},
                {data: 'purchased_at', name: 'purchased_at'},
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        let showRoute = '{{ route('admin.investment-seats.show', ':id') }}'.replace(':id', data);
                        return `<a class="btn btn-sm btn-outline-primary" href="${showRoute}"><i class="feather icon-eye"></i></a>`;
                    }
                }
            ]
        });
    });
</script>
