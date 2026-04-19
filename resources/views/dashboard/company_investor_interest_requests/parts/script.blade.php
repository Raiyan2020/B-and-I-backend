<script>
    $(document).ready(function() {
        const table = $('#company-investor-interest-requests-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.company-investor-interest-requests.index') }}",
                data: function(d) {
                    d.company_id = $('#company-id-filter').val() || '';
                    d.company_name = $('#company-name-filter').val() || '';
                    d.investor_name = $('#investor-name-filter').val() || '';
                    d.interest_date = $('#interest-date-filter').val() || '';
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {
                    data: 'company_name',
                    name: 'company.first_name',
                    defaultContent: '-',
                    render: function(data, type, row) {
                        if (type !== 'display' || !row.company_show_url || data === '-') {
                            return data ?? '-';
                        }

                        return `<a href="${row.company_show_url}" class="text-primary fw-semibold">${data}</a>`;
                    }
                },
                {
                    data: 'investor_name',
                    name: 'investor.first_name',
                    defaultContent: '-',
                    render: function(data, type, row) {
                        if (type !== 'display' || !row.investor_show_url || data === '-') {
                            return data ?? '-';
                        }

                        return `<a href="${row.investor_show_url}" class="text-primary fw-semibold">${data}</a>`;
                    }
                },
                {data: 'created_at', name: 'created_at'}
            ]
        });

        $('#filter-btn').on('click', function() {
            table.ajax.reload(null, false);
        });

        $('#reset-filter-btn').on('click', function() {
            $('#company-id-filter').val('');
            $('#company-name-filter').val('');
            $('#investor-name-filter').val('');
            $('#interest-date-filter').val('');
            table.ajax.reload(null, false);
        });
    });
</script>
