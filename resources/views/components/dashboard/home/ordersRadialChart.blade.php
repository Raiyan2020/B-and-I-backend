<div class="col-lg-4 col-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between pb-0">
            <h4>{{__('dashboard.orders')}}</h4>
{{--            <div class="dropdown chart-dropdown">--}}
{{--                <button class="btn btn-sm border-0 dropdown-toggle p-0" type="button"--}}
{{--                        id="dropdownItem2" data-toggle="dropdown" aria-haspopup="true"--}}
{{--                        aria-expanded="false">--}}
{{--                    Last 7 Days--}}
{{--                </button>--}}
{{--                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownItem2">--}}
{{--                    <a class="dropdown-item" href="#">Last 28 Days</a>--}}
{{--                    <a class="dropdown-item" href="#">Last Month</a>--}}
{{--                    <a class="dropdown-item" href="#">Last Year</a>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
        <div class="card-content">
            <div class="card-body">
                <div id="order-status-chart" class="mb-3"></div>
                <div class="chart-info d-flex justify-content-between mb-1">
                    <div class="series-info d-flex align-items-center">
                        <i class="fa fa-circle-o text-bold-700 text-primary"></i>
                        <span class="text-bold-600 ml-50">{{__('dashboard.Pending')}}</span>
                    </div>
                    <div class="product-result">
                        <span>{{$orderDetail['pending']}}</span>
                    </div>
                </div>
                <div class="chart-info d-flex justify-content-between mb-1">
                    <div class="series-info d-flex align-items-center">
                        <i class="fa fa-circle-o text-bold-700 text-warning"></i>
                        <span class="text-bold-600 ml-50">{{__('dashboard.Finished')}}</span>
                    </div>
                    <div class="product-result">
                        <span>{{$orderDetail['finished']}}</span>
                    </div>
                </div>
                <div class="chart-info d-flex justify-content-between mb-75">
                    <div class="series-info d-flex align-items-center">
                        <i class="fa fa-circle-o text-bold-700 text-danger"></i>
                        <span class="text-bold-600 ml-50">{{__('dashboard.Rejected')}}</span>
                    </div>
                    <div class="product-result">
                        <span>{{$orderDetail['canceled']}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

