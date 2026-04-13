<script>
    $(document).ready(function() {
        $('#investment-seats-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.investment-seats.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'opportunity_name', name: 'opportunity.company_name', defaultContent: '-'},
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
