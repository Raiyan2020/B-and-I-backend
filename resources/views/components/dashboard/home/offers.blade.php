<div class="col-md-4 col-12">
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="row pb-50">
                    <div class="col-lg-6 col-12 d-flex justify-content-between flex-column order-lg-1 order-2 mt-lg-0 mt-2">
                        <div>
                            <h2 class="text-bold-700 mb-25">{{$offers->count()}}</h2>
                            <p class="text-bold-500 mb-75">{{__('dashboard.offers')}}</p>
                            <h5 class="font-small-2">
                                <span class="text-success">{{__('dashboard.users attract')}} </span>
                            </h5>
                        </div>
                        <a href="{{route('admin.offers.index')}}" class="btn btn-primary shadow">{{__('dashboard.View More')}} <i class="feather icon-chevrons-right"></i></a>
                    </div>
                </div>
                <hr/>
                @if($offers->count() > 0)
                    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" style="height: 375px">
                        <ol class="carousel-indicators">
                            @foreach($offers as $key=>$offer)
                                <li data-target="#carousel-example-generic" data-slide-to="{{$key}}"></li>
                            @endforeach
                        </ol>

                        <div class="carousel-inner" role="listbox">
                            <div class="carousel-item active">
                                <img src="{{$offers[0]->image}}" class="d-block w-100" height="250px" alt="1 slide">
                                <hr/>
                                <p class="card-text">{{$offers[0]->description}}.</p>
                            </div>
                            @foreach($offers as $key=>$offer)
                                @if($key != 0)
                                    <div class="carousel-item">
                                        <img src="{{$offer->image}}" class="d-block w-100" height="250px" alt="{{$offer->id}} slide">
                                        <hr/>
                                        <p class="card-text">{{$offer->description}}.</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <a class="carousel-control-prev" href="#carousel-example-generic" role="button" data-slide="prev">
                            <span class="fa fa-angle-left icon-prev" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carousel-example-generic" role="button" data-slide="next">
                            <span class="fa fa-angle-right icon-next" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                @else
                    <img src="{{asset('dashboardAssets/images/defaultOffers.jpg')}}" class="d-block w-100" height="250px" alt="1 slide">
                    <hr/>
                    <p class="card-text">Offers Descriptions her.</p>
                @endif

                <div class="row avg-sessions pt-50 ">
                    <div class="col-6">
                        <p class="mb-0">Active : {{$offers->where('active',1)->count()}}</p>
                        <div class="progress progress-bar-primary mt-25">
                            <div class="progress-bar" role="progressbar" aria-valuenow="50"
                                 aria-valuemin="50" aria-valuemax="100" style="width:{{$offers->where('active',1)->count()}}0%"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <p class="mb-0">In-Active : {{$offers->where('active',0)->count()}}</p>
                        <div class="progress progress-bar-warning mt-25">
                            <div class="progress-bar" role="progressbar" aria-valuenow="60"
                                 aria-valuemin="60" aria-valuemax="100" style="width:{{$offers->where('active',0)->count()}}0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
