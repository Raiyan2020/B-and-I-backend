<div class="col-lg-3 col-md-6 col-12">
    <div class="card">
{{--        <span class="text-muted m-1" style="text-align: end">Latest 4 Months</span>--}}
        <div class="card-header d-flex flex-column align-items-start pb-0">
            <div class="avatar bg-rgba-primary p-50 m-0">
                <div class="avatar-content">
                    <i class="feather icon-users text-primary font-medium-5"></i>
                </div>
            </div>
            <h2 class="text-bold-700 mt-1 mb-25">{{$users->count()}}</h2>
            <p class="mb-0">{{__('dashboard.users')}}</p>
        </div>
        <div class="card-content">
            <div id="users-gain-chart"></div>
        </div>
    </div>
</div>

