@extends('adminlte::page')

@section('title', 'Stores')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<style>
    .table td {
        padding: 0;
        padding-left: 4px;
    }

    .table th {
        padding: 2;
        padding-left: 3px;
    }
</style>
@stop

@section('content_header')
<h1 class="m-0 text-dark">Stores Price Updated</h1>

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

        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr class="table-info">
                    <th>ID</th>
                    <th>ASIN</th>
                    <th>Product SKU</th>
                    <th>Push Price</th>
                    <th>Feedback ID</th>
                    <th>Feedback Responce</th>
                    <th>Feedback Availability ID</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')
<script type="text/javascript">
    $(function() {

        $.extend($.fn.dataTable.defaults, {
            pageLength: 100,
        });


        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('buybox.store.listing.updated') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'asin',
                    name: 'asin'
                },
                {
                    data: 'product_sku',
                    name: 'product_sku'
                },
                {
                    data: 'push_price',
                    name: 'push_price'
                },
                {
                    data: 'feedback_price_id',
                    name: 'feedback_price_id'
                },
                {
                    data: 'feedback_response',
                    name: 'feedback_response'
                },
                {
                    data: 'feedback_availability_id',
                    name: 'feedback_availability_id'
                },
            ]
        });

    });
</script>
@stop