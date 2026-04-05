@php use App\Models\Order; @endphp
    <!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="rtl">
<!-- BEGIN: Head-->
<head>
    <title>Customers PDF</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;700&display=swap');

        body {
            padding: 1rem;
        }


        table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
            direction: {{app()->getLocale() == 'ar'?'rtl':'ltr'}};
            @if(app()->getLocale() == 'ar')
                font-family: 'XBRiyaz';
            @endif

        }

        tr:nth-child(even) {
            background: #f1f7f8;
        }

        th,
        td {
            border: 1px solid #a0c8cf;
            padding: .75rem;
            text-align: center;
        }

        th {
            background: #74afb9;
            color: #fff;
            font-weight: bold;
            font-size: 20px;
        }

        td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        h2 {
            text-align: center;
            @if(app()->getLocale() == 'ar')
                font-family: 'XBRiyaz';
            @endif
       }
        .logo{
            width: 60px;
            height: 60px;
            display: flex;
            border: 1px solid lightgrey;
            border-radius: 50%;
            overflow: hidden;
            background-size: cover;
            @if(app()->getLocale() == 'ar')
                margin-left: auto;
            @endif
            {{--background-image: url("{{url('public/dashboardAssets')}}");--}}
        }

    </style>
</head>
<!-- END: Head-->

<body>
{{--<div class="logo">--}}
{{--</div>--}}
    <h2>{{__('dashboard.users')}}</h2>
<table>
    <tr>
        <th>{{__('dashboard.id')}}</th>
        <th>{{__('dashboard.table name')}}</th>
        <th style="width: 150px;min-width: 150px">{{__('dashboard.table phone')}}</th>
        <th>{{__('dashboard.table email')}}</th>
        <th style="width: 220px;min-width: 220px">{{__('dashboard.table address')}}</th>
        <th  style="width: 170px;min-width: 170px">{{__('dashboard.orders count')}}</th>
        <th>{{__('dashboard.table status')}}</th>
        <th style="width: 170px;min-width: 170px">{{__('dashboard.table create date')}}</th>
    </tr>
    @foreach($customers as $key=>$customer)
        <tr>
            <td>{{$key+1}}</td>
            <td>{{$customer->name}}</td>
            <td>{{$customer->phone}}</td>
            <td>{{$customer->email}}</td>
            <td style="white-space: pre-wrap">{{$customer->address}}</td>
            <td>{{$customer->orders->where('order_status_id',Order::FINISHED)->count()}}</td>
            <td>{{$customer->block == 0 ?'ACTIVE':'BLOCKED'}}</td>
            <td>{{$customer->created_at}}</td>
        </tr>
    @endforeach
</table>
</body>
<!-- END: Body-->
</html>
