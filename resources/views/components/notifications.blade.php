<li class="dropdown dropdown-notification nav-item"><a class="nav-link nav-link-label" href="#" data-toggle="dropdown"><i class="ficon feather icon-bell"></i><span class="badge badge-pill badge-primary badge-up" id="notification-counter">{{$notifications->where('seen',0)->count()}}</span></a>
    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
        <li class="dropdown-menu-header">
            <div class="dropdown-header m-0 p-2">
                <h3 class="white">{{$notifications->where('seen',0)->count() .' '.__('dashboard.New')}}</h3><span class="notification-title">{{__('dashboard.App Notifications')}}</span>
            </div>
        </li>
        <li class="scrollable-container media-list">
            @foreach($notifications->where('seen',0) as $notification)
            <a class="d-flex justify-content-between notification-link" id="{{$notification->id}}" href="{{$notification->order_id > 0?route('admin.orders.show',$notification->order_id):route('admin.notifications.read',$notification->id)}}">
                <div class="media d-flex align-items-start">
                    <div class="media-left"><i class="feather icon-x-circle font-medium-5 primary"></i></div>
                    <div class="media-body">
                        <h6 class="primary media-heading">{{$notification->title}}!</h6><small class="notification-text">
                        {{$notification->body}}</small>
                    </div><small>
                        <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">{{$notification->created_at}}</time></small>
                </div>
            </a>
            @endforeach
            @foreach($notifications->where('seen',1) as $notification)
            <a class="d-flex justify-content-between notification-link" id="{{$notification->id}}" href="{{$notification->order_id > 0?route('admin.orders.show',$notification->order_id):route('admin.notifications.read',$notification->id)}}">
                <div class="media d-flex align-items-start">
                    <div class="media-left"><i class="feather icon-check-circle font-medium-5 info"></i></div>
                    <div class="media-body">
                        <h6 class="info media-heading">{{$notification->title}}</h6><small class="notification-text">{{$notification->body}}</small>
                    </div><small>
                        <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">{{$notification->created_at}}</time></small>
                </div>
            </a>
            @endforeach

        </li>
        <li class="dropdown-menu-footer"><a class="dropdown-item p-1 text-center" href="{{route('admin.notifications.read_all')}}">{{__('dashboard.Read all notifications')}}</a></li>
    </ul>
</li>
