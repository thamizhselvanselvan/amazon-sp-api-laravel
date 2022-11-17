@extends('adminlte::page')

@section('title', 'Amazon Orders Statistics')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<style>
    .table td {
        padding: 0;
        padding-left: 5px;
    }

    .table th {
        padding: 2;
        padding-left: 5px;
    }

    .click {
        color: green;
    }

    .not {
        color: black;
    }

    .under {
        color: blue;
    }

    .wrong {
        color: red;
    }
</style>
@stop

@section('content_header')
<div class="row">
    <div class="col-1.5">
        <div style="margin-top: 1.6rem;">
            <h3 class="m-0 text-dark font-weight-bold">
                Orders Status: &nbsp;
            </h3>
        </div>
    </div>

    <!-- <form class="row"> -->
    <div class="col-2.5">

        <x-adminlte-select name="ware_id" id="store_select" label="">
            <option value="">Select Store</option>
            @foreach($stores as $store)
            <option value="{{$store->store_id}}" {{ $request_store_id == $store->store_id ? "selected" : '' }}>{{$store->store_name}}</option>
            @endforeach
        </x-adminlte-select>
    </div>
    <!-- </form> -->
</div>

@stop
@section('content')

<div class="row">
    <div class="col">

        <div class="alert_display">
            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>

<table class="table table-bordered yajra-datatable table-striped" id="detail_table">
    <thead>
        <tr class="table-info">
            <th>ID</th>
            <th>Store Name</th>
            <th>Order Date</th>
            <th>Amazon Order ID</th>
            <th>Order Item ID</th>
            <th>Courier</th>
            <th>AWB</th>
            <th>Booking Status</th>
            <!-- <th>Zoho ID</th> -->
            <th>Zoho</th>
            <th>Feed Status</th>
            <th>Updated Time</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
@stop

@section('js')

<script type="text/javascript">
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });


    $.extend($.fn.dataTable.defaults, {
        pageLength: 50,
    });

    $('#store_select').on('change', function() {

        window.location = "/orders/statistics/" + $(this).val();

    });

    let yajra_table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        lengthChange: false,
        // searching: false,
        ajax: {
            url: "{{ url($url) }}",
            type: 'get',
            headers: {
                'content-type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.store_id = $('#store_select').val();
            },
        },
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'store_name',
                name: 'store_name'
            },
            {
                data: 'order_date',
                name: 'order_date'
            },
            {
                data: 'amazon_order_id',
                name: 'amazon_order_id'
            },
            {
                data: 'order_item_id',
                name: 'order_item_id'
            },
            {
                data: 'courier_name',
                name: 'courier_name'
            },
            {
                data: 'courier_awb',
                name: 'courier_awb'
            },
            {
                data: 'booking_status',
                name: 'booking_status'
            },
            // {
            //     data: 'zoho_id',
            //     name: 'zoho_id',
            // },
            {
                data: 'zoho_status',
                name: 'zoho_status',
            },
            {
                data: 'order_feed_status',
                name: 'order_feed_status'
            },
            {
                data: 'updated_at',
                name: 'updated_at'
            }
        ]
    });
</script>
@stop