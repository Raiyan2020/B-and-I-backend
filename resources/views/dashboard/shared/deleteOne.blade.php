<script>
    $(document).on('click' , '.delete-row', function (e) {
        e.preventDefault()
        Swal.fire({
            title: "{{__('dashboard.swal_title')}}",
            text: "{{__('dashboard.swal_text')}}",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{__('dashboard.confirm')}}',
            confirmButtonClass: 'btn btn-primary',
            cancelButtonText: '{{__('dashboard.cancel')}}',
            cancelButtonClass: 'btn btn-danger ml-1',
            }).then( (result) => {
            if (result.value) {
                $.ajax({
                    type: "delete",
                    url: $(this).data('url'),
                    data: {
                        _token: '{{csrf_token()}}'
                    },
                    dataType: "json",
                    success:  (response) => {

                        Swal.fire(
                        {
                            position: 'top-start',
                            icon: response.error??'success',
                            title: response.error??'success',
                            text: response.msg??'{{__('dashboard.item deleted successfully')}}',
                            showConfirmButton: false,
                            timer: 1500,
                            confirmButtonClass: 'btn btn-primary',
                        })
                        // toastr.error()
                        if (!response.error){
                            $(this).closest('td').parent('tr').remove();
                            $(this).closest('table').DataTable().draw();
                        }
                    }
                });
            }
        })
    });
</script>
