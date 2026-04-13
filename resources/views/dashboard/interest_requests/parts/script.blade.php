<script>
    $(document).ready(function() {
        $('#interest-requests-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.interest-requests.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'opportunity_name', name: 'opportunity.company_name', defaultContent: '-'},
                {data: 'advertiser_name', name: 'opportunity.user.first_name', defaultContent: '-'},
                {data: 'investor_name', name: 'user.first_name', defaultContent: '-'},
                {data: 'seat_reference', name: 'investment_seat_id'},
                {data: 'created_at', name: 'created_at'},
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        let showRoute = '{{ route('admin.interest-requests.show', ':id') }}'.replace(':id', data);
                        return `<a class="btn btn-sm btn-outline-primary" href="${showRoute}"><i class="feather icon-eye"></i></a>`;
                    }
                }
            ]
        });
    });
</script>
