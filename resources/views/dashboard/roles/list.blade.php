<x-dashboard.layouts.master title="{{__('dashboard.roles list')}}">
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- users list start -->
                <section class="users-list-wrapper">
                    <x-dashboard.layouts.breadcrumb now="{{__('dashboard.roles list')}}">
                    </x-dashboard.layouts.breadcrumb>
                    <!-- Column selectors with Export Options and print table -->
                    <section id="column-selectors">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">{{__('dashboard.roles list')}}</h4>
                                    </div>
                                    @if(\Session::get('success'))
                                        <x-dashboard.layouts.message />
                                    @endif
                                    <div class="card-content">
                                        <div class="card-body card-dashboard">

                                            <div class="table-responsive overflow-auto">
                                                @can('add-role')
                                                <a href="{{route('admin.roles.create')}}"><button  class="mb-2 btn btn-primary"><i class="mr-1 feather icon-plus"></i>{{__('dashboard.add roles')}}</button></a>
                                                    @endcan
                                                <table class="table table-striped " id="roles-table">

                                                    <thead >


                                                    <tr class="text text-center">
                                                        <th>{{__('dashboard.table name')}}</th>
                                                        <th>{{__('dashboard.users count')}}</th>
                                                        <th>{{__('dashboard.table create date')}}</th>
                                                        <th>{{__('dashboard.actions')}}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="text text-center ">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Column selectors with Export Options and print table -->
                </section>
                <!-- users list ends -->

            </div>
        </div>
    </div>
    <!-- END: Content-->
    @push('vendor-styles')
    <!-- DataTables CSS (Page-specific)-->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboardAssets/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    @endpush

    @push('vendor-scripts')
    <!-- DataTables JS (Page-specific)-->
    <script src="{{asset('dashboardAssets/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
    <script src="{{asset('dashboardAssets/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js')}}"></script>
    @endpush

    @push('page-styles')
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/custom/css/admins-index.css') }}">
    @endpush

    @push('page-scripts')
    <script>
        $(document).ready(function () {
                $('#roles-table').DataTable({
                    processing: true,
                    serverSide: true,
                    lengthMenu: [10, 20, 40, 60, 80, 100],
                    pageLength: 10,
                    ajax: {
                        url :"{{ route('admin.roles.index') }}",
                        headers:{'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
                        data: function (d) {
                            d.page = 1;
                        }
                    },
                    "paging": true,
                    order : [[2,'asc']],
                    columns: [
                        {data: 'display_name', name:'display_name'},
                        {data: 'users_count', name:'users_count',
                            render:function (data){
                                return `<span class="badge badge-primary text text-center font-medium-2">${data}</span><i class="fa fa-users fa-2x text text-primary ml-2"></i>`;
                            }},
                        {data: 'created_at',name: 'created_at'},
                        {data: 'id',
                            orderable: false,
                            searchable: false,
                            render:function (data, type, row){
                                if (data <= 1) {
                                    return '';
                                }
                                var editUrl = '{{ route('admin.roles.edit', ':id') }}'.replace(':id', data);
                                var deleteUrl = '{{ route('admin.roles.destroy', ':id') }}'.replace(':id', data);
                                var canEdit = @json(auth()->user()->can('edit-role'));
                                var canDelete = @json(auth()->user()->can('delete-role'));
                                var hasAdmins = parseInt(row.users_count, 10) > 0;
                                if (!canEdit && !canDelete) {
                                    return '';
                                }
                                var html = '<div class="d-flex align-items-center justify-content-center flex-wrap">';
                                if (canEdit) {
                                    html += '<a class="btn btn-sm btn-icon btn-outline-primary mr-1 mb-1" href="' + editUrl + '" title="{{ __('dashboard.edit') }}"><i class="feather icon-edit text-primary"></i></a>';
                                }
                                if (canDelete) {
                                    if (!hasAdmins) {
                                        html += '<button type="button" class="btn btn-sm btn-icon btn-outline-danger mb-1 delete-row" data-url="' + deleteUrl + '" title="{{ __('dashboard.delete') }}"><i class="feather icon-trash-2 text-danger"></i></button>';
                                    } else {
                                        html += '<span class="btn btn-sm btn-icon btn-outline-secondary mb-1" style="opacity:0.55;cursor:not-allowed" title="{{ __('dashboard.cannot_delete_role_assigned_to_admins') }}"><i class="feather icon-trash-2 text-secondary"></i></span>';
                                    }
                                }
                                html += '</div>';
                                return html;
                            }
                        },
                    ]
                });
            });


        </script>
    @endpush
</x-dashboard.layouts.master>
