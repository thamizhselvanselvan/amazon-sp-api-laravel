@extends('adminlte::page')
@section('title', 'Label')

@section('content_header')
<div class="invoice-company text-inverse f-w-600">
    <span class="pull-right hidden-print">
        <a href="javascript:void(0);" class="btn btn-sm btn-white m-b-10 p-l-5" id="Export_to_pdf"><i class="fa fa-file t-plus-1 text-danger fa-fw fa-lg"></i> Export as PDF</a>
        <a href="javascript:;" onclick="window.print()" class="btn btn-sm btn-white m-b-10 p-l-5"><i class="fa fa-print t-plus-1 fa-fw fa-lg"></i> Print</a>

    </span>
    <br>
</div>
@stop

@section('css')
<style type="text/css">
    
</style>
@stop

@section('content')
<input type="hidden" id="awb_no" value="{{$awb_no}}">

<div class="container " id="label-container">
    <div class="col-md-12">
        <div class="invoice p-2">
            <div class="invoice-content ">
                <!-- <div class="table-responsive"> -->
                <table class="table table-invoice table-bordered table-bordered-dark mb-1">
                    <tbody >
                        <tr>
                            <td class="pb-0">
                                <div class="row">
                                    <div class="col"></div>
                                    <div class="col">{!! $bar_code !!}
                                        <b>
                                            <div class="text-center">{{ $result->awb_no }}</div>
                                        </b>
                                    </div>
                                    <div class="col"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="pt-1 pb-1">
                                <div class="row ">
                                    <div class="col">
                                        <div class="text-inverse m-b-5 text-left"><strong>
                                                Invoice No: </strong> {{$result->order_no}}
                                        </div>
                                        <div class="text-inverse m-b-5 text-left"><strong>
                                                Order Date: </strong>{{date('Y-m-d', strtotime($result->purchase_date))}}
                                        </div>
                                        <div class="text-inverse m-b-5 text-left"><strong> Price: </strong>
                                            @if ($result->order_total)
                                            {{$result->order_total->CurrencyCode}} {{$result->order_total->Amount}}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="pt-1 pb-1">
                                <div class="row">
                                    <div class="col">
                                        <strong>Ship To: </strong><br>
                                        <strong>{{$result->shipping_address['Name']}}</strong><br>
                                        @if(isset($result->shipping_address['AddressLine1']))
                                        {{$result->shipping_address['AddressLine1']}},
                                        @endif

                                        @if(isset($result->shipping_address['AddressLine2']))
                                        {{$result->shipping_address['AddressLine2']}}
                                        @endif
                                        <br>
                                        <strong>City: </strong>
                                        @if(isset($result->shipping_address['City']))
                                        {{$result->shipping_address['City']}}
                                        @else
                                        NA
                                        @endif
                                        <br>
                                        @if(isset($result->shipping_address['County']))
                                        <strong>County: </strong>
                                        {{$result->shipping_address['County']}},
                                        @endif

                                        @if(isset($result->shipping_address['CountryCode']))
                                        {{$result->shipping_address['CountryCode']}}
                                        @endif
                                        <br>
                                        @if(isset($result->shipping_address['Phone']))
                                        <strong>Phone: </strong>
                                        {{$result->shipping_address['Phone']}}
                                        @endif
                                        <br>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered table-bordered-dark">
                    <thead>
                        <tr>
                            <th class="text-left">Sr</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">SKU</th>
                            <th class="text-center" width="10%">QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result->product as $key => $value)
                        <tr>
                            <td class="text-center p-1">{{$key+1}}</td>
                            <td class="p-1">{{$value['title']}}</td>
                            <td class="text-center p-1">{{$value['sku']}}</td>
                            <td class="text-center p-1">{{$value['qty']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tr>
                        <td class="mt-1 p-1 small" colspan="4"><strong>Return Address:</strong> Mahzuz, Al Habtoor Warehouse No.27 ,Al QusaisIndustrial Area 3 mumbai, MH, IN, 400025</td>
                    </tr>
                </table>
                <!-- </div> -->
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#Export_to_pdf').click(function(e) {
            e.preventDefault();
            var url = $(location).attr('href');
            var awb_no = $('#awb_no').val();
            // alert(url);
            $.ajax({
                method: 'POST',
                url: "{{ url('/label/export-pdf')}}",
                data: {
                    'url': url,
                    'awb_no': awb_no,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {

                    window.location.href = '/label/download/' + awb_no;
                    alert('Download pdf successfully');
                }
            });
        });
    });
</script>
@stop