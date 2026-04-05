@php use App\Models\Order; @endphp
<table class="table table-hover-animation mb-0">
    <thead>
    <tr>
        <th scope="col" style="width:70px; text-align: center">{{__('dashboard.id')}}</th>
        <th scope="col" style="width: 120px; text-align: center">{{__('dashboard.table name')}}</th>
        <th scope="col" style="width:100px; text-align: center">{{__('dashboard.table phone')}}</th>
        <th scope="col" style="width:200px; text-align: center">{{__('dashboard.table email')}}</th>
        <th scope="col" style="width:350px; text-align: center">{{__('dashboard.table address')}}</th>
        <th scope="col" style="width:80px; text-align: center">{{__('dashboard.orders count')}}</th>
        <th scope="col" style="width:80px; text-align: center">{{__('dashboard.table status')}}</th>
        <th scope="col" style="width:100px; text-align: center">{{__('dashboard.table create date')}}</th>
    </tr>
    </thead>
    <tbody>
        @foreach($customers as $key=>$customer)
            <tr>
                <th scope="row" style="text-align: center">{{$key+1}}</th>
                <td style="text-align: center">{{$customer->name}}</td>
                <td style="text-align: center">{{$customer->phone}}</td>
                <td style="text-align: center">{{$customer->email}}</td>
                <td style="text-align: center">{{$customer->address}}</td>
                <td style="text-align: center">{{$customer->orders->where('order_status_id',Order::FINISHED)->count()}}</td>
                <td style="text-align: center">{{$customer->block == 0 ?'ACTIVE':'BLOCKED'}}</td>
                <td style="text-align: center">{{$customer->created_at}}</td>
            </tr>
        @endforeach
    </tbody>
</table>

