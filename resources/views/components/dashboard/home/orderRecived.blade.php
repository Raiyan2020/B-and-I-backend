<div class="col-lg-3 col-md-6 col-12">
    <div class="card">
        <div class="card-header d-flex flex-column align-items-start pb-0">
            <div class="avatar bg-rgba-warning p-50 m-0">
                <div class="avatar-content">
                    <i class="feather icon-package text-warning font-medium-5"></i>
                </div>
            </div>
            <h2 class="text-bold-700 mt-1 mb-25">{{$orders->count()}}</h2>
            <p class="mb-0">{{__('dashboard.orders count')}}</p>
        </div>
        <div class="card-content">
            <div id="ordersChart"></div>
        </div>
    </div>
</div>
