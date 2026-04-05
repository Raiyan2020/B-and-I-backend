<x-dashboard.layouts.master title="{{__('dashboard.app notifications')}}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
              <x-dashboard.layouts.breadcrumb now="{{__('dashboard.app notifications')}}">
              </x-dashboard.layouts.breadcrumb>
             <div class="content-body">
                 <section id="basic-horizontal-layouts">
                     <div class="row match-height justify-content-center">
                        <div class="col-md-6 col-12">
                             <div class="card">
                                <div class="card-header  mb-5">
                                    <h4 class="card-title">{{__('dashboard.app notifications')}}</h4>
                                </div>
                                @if(\Session::get('success'))
                                    <x-dashboard.layouts.message />
                                @endif
                                <div class="card-content">
                                    <div class="card-body">
                                          <form class="form form-horizontal" method="POST" action="{{route('admin.appNotifications.send')}}">
                                             @csrf
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12  mb-3">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>{{__('dashboard.title').__('dashboard.in arabic')}}</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="title_ar" class="form-control border-0" style="border-bottom: 1px solid grey !important;" name="title_ar" placeholder="{{__('dashboard.title')}}">
                                                            </div>
                                                            @error('title_ar')
                                                            <span class="alert alert-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12  mb-3">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>{{__('dashboard.title').__('dashboard.in english')}}</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="title_en" class="form-control border-0" style="border-bottom: 1px solid grey !important;" name="title_en" placeholder="{{__('dashboard.title')}}">
                                                            </div>
                                                            @error('title_en')
                                                            <span class="alert alert-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12  mb-3">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>{{__('dashboard.body').__('dashboard.in arabic')}}</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <textarea class="form-control border-0" style="border-bottom: 1px solid grey !important;" rows="3" placeholder="{{__('dashboard.body')}}" name="body_ar"></textarea>
                                                            </div>
                                                            @error('body_ar')
                                                            <span class="alert alert-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12  mb-3">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>{{__('dashboard.body').__('dashboard.in english')}}</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <textarea class="form-control border-0" style="border-bottom: 1px solid grey !important;" rows="3" placeholder="{{__('dashboard.body')}}" name="body_en"></textarea>
                                                            </div>
                                                            @error('body_en')
                                                            <span class="alert alert-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12  mb-3">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>{{__('dashboard.send to')}}</span>
                                                            </div>
                                                             <div class="col-md-8">
                                                                  <select id="send-to" class="select2 form-control " name="send_to">
                                                                      <option value="0" selected>{{__('dashboard.all')}}</option>
                                                                      <option value="1">{{__('dashboard.somebody')}}</option>
                                                                 </select>
                                                            </div>
                                                            @error('send_to')
                                                            <span class="alert alert-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-3" id="users">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>{{__('dashboard.users')}}</span>
                                                            </div>
                                                             <div class="col-md-8">
                                                                 <select id="users-select" class="select2 form-control" name="users_select[]" multiple>

                                                                 </select>
                                                             </div>
                                                                @error('users_select')
                                                                 <span class="alert alert-danger">{{$message}}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                         <div class="col-md-8 offset-md-4">
                                                               <button type="submit" class="btn btn-primary mr-1 mb-1">{{__('dashboard.send')}}</button>
                                                         </div>
                                                      </div>
                                                  </div>
                                              </form>
                                          </div>
                                      </div>
                                 </div>
                              </div>
                         </div>
                     </section>
                </div>
           </div>
      </div>
    @push('vendor-styles')
    <!-- Select2 CSS (Page-specific)-->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboardAssets/app-assets/vendors/css/forms/select/select2.min.css') }}">
    @endpush

    @push('vendor-scripts')
    <!-- Select2 JS (Page-specific)-->
    <script src="{{asset('dashboardAssets/app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
    <script src="{{asset('dashboardAssets/app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
    @endpush

    @push('page-scripts')
    <script>
        $(document).ready(function () {
            $('#users').hide();
            $('#send-to').on('change', function () {
                var selected = $(this).find('option:selected');
                let option = selected.val();
                console.log(option);
                if (option == 1){
                    $.ajax({
                        url: "{{route('admin.users.getUsers')}}",
                        type: "GET",
                        data: {},
                        success: function (response) {
                            $('#users').show();
                            $("#users-select").empty();
                            if (response) {
                                $("#users-select").append("<option value='' disabled selected>{{__('dashboard.choose option')}}</option>")
                                $.each(response, function (j, i) {
                                    $("#users-select").append("<option value='" + i.id + "'>" + i.name + "</option>");
                                });
                            }
                        }
                    });
                }
                if(option == 0){
                    $('#users').hide();
                }
            });
        });
    </script>
    @endpush
</x-dashboard.layouts.master>
