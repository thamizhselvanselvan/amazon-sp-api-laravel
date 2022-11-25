@extends('adminlte::page')
@section('title', 'Label')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class="row mb-4">
    <div class="col-0.5">
        <div style="margin-top: 0.3rem;">
            <a href="{{ route('label.manage') }}">
                <x-adminlte-button label='Back' class="btn-sm" theme="primary" icon="fas fa-long-arrow-alt-left" type="submit" />
            </a>
        </div>
    </div>
    <div class="col-3">
        <h1 class=" text-dark text-center">Label Search By Date</h1>
    </div>
</div>

<!--  Edit address modal start -->
<div class="modal fade " id="crud-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="customerCrudModal">Order Address Details Editer</h4>
            </div>
            <div class="modal-body">

                <div class="text-center">
                    <div id="spinner-container" class="spinner-border justify-content-center" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="form-content" style="display: none">
                    <div id="warning" class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Be carefull!</strong> changes canot be reverted back ....
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form name="orderAddressForm" id="orderAddressForm" method="POST" action="javascript:void(0)">
                        <input type="hidden" name="order_item_identifier" id="order_item_identifier">
                        <input type="hidden" name="amazon_order_identifier" id="amazon_order_identifier">
                        @csrf
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Name:</strong>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name" onchange="validate()">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Phone:</strong>
                                    <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone" onchange="validate()">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>City:</strong>
                                    <input type="text" name="city" id="city" class="form-control" placeholder="City" onchange="validate()">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>County:</strong>
                                    <input type="text" name="county" id="county" class="form-control" placeholder="County" onchange="validate()">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>CountryCode:</strong>
                                    <input type="text" name="countryCode" id="countryCode" class="form-control" placeholder="CountryCode" onchange="validate()">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>AddressType:</strong>
                                    <input type="text" name="addressType" id="addressType" class="form-control" placeholder="AddressType" onchange="validate()">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>AddressLine1:</strong>
                                    <textarea name="addressLine1" id="addressLine1" class="form-control" placeholder="AddressLine1" onchange="validate()"></textarea>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>AddressLine2:</strong>
                                    <textarea name="addressLine2" id="addressLine2" class="form-control" placeholder="AddressLine2" onchange="validate()"></textarea>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                <button type="submit" id="btn-update-order" name="btnsave" class="btn btn-primary">Update</button>
                                <a id="closemodal" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  Edit address modal End -->

@stop
@section('content')

@csrf
<div class="row">
    <div class="col-2 float-left mt-2">
        <label>Select Date:</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="far fa-calendar-alt"></i>
                </span>
            </div>
            <input type="text" class="form-control float-right datepicker" name='export_date' placeholder="Select Date Range" autocomplete="off" id="search_date">

        </div>
    </div>
    <div class="col-2 float-left mt-2">
        <div style="margin-top: 2.2rem;">
            <x-adminlte-button label="search" theme="success" class="btn btn-sm " icon="fas fa-search" type="submit" id="label_search" />
        </div>
    </div>
</div>

<div id="showTable" class="d-none">
    <table class='table table-bordered table-striped text-center ' id='label_search'>
        <thead>
            <tr class='text-bold bg-info'>

                <th>Store Name</th>
                <th>Order No.</th>
                <th>Awb No.</th>
                <th>Courier Name</th>
                <th>Order Date</th>
                <th>Customer</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id='label_table'>
        </tbody>
    </table>
</div>
@stop

@section('js')
<script type="text/javascript">
    $('#label_table').on('click', '#edit-address', function() {

        var order_item_identifier = $(this).data('id');
        var amazon_order_identifier = $(this).data('amazon_order_identifier');
        loadOrderAddressFormFunction(order_item_identifier, amazon_order_identifier);

        $('#danger').hide();
        $('#success').hide();

    });

    function loadOrderAddressFormFunction(order_item_identifier, amazon_order_identifier) {

        $('#form-content').hide();
        $('#spinner-container').show();

        $.get('/label/edit-order-address-search-id/' + order_item_identifier, function(data) {

            $('#order_item_identifier').val(order_item_identifier);
            $('#amazon_order_identifier').val(amazon_order_identifier);
            $('#name').val(data.Name);
            $('#phone').val(data.Phone);
            $('#county').val(data.County);
            $('#countryCode').val(data.CountryCode);
            $('#city').val(data.City);
            $('#addressType').val(data.AddressType);
            $('#addressLine1').val(data.AddressLine1);
            $('#addressLine2').val(data.AddressLine2);

            setTimeout(function() {
                $('#form-content').show();
                $('#spinner-container').hide();
            }, 50); // How long you want the delay to be, measured in milliseconds.
        });
        $('#crud-modal').modal('show');
    }

    $("#orderAddressForm").submit(function() {
        var order_item_identifier = $('#order_item_identifier').val();
        var amazon_order_identifier = $('#amazon_order_identifier').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#btn-update-order').html(
            "<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Please wait"
        );

        $("#btn-update-order").attr("disabled", true);
        $.ajax({
            url: "/label/update-order-address-search-id/" + amazon_order_identifier,
            type: "PUT",
            data: $('#orderAddressForm').serialize(),
            success: function(response) {
                if (response.status == 400) {
                    $('#success').hide();
                    $('#danger').hide();
                    var errors = '<ul>'
                    $.each(response.errors, function(key, err_values) {
                        errors += '<li>' + err_values + '</li>';
                    });
                    errors += '</ul>'

                    $(
                        `<div id="danger" class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong> Validation Failed!</strong> 
                                                ` + errors + `
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>`
                    ).insertAfter("#warning");
                } else if (response.status == 200) {
                    $('#danger').hide();
                    $('#success').hide();
                    $(
                        `<div id="success" class="alert alert-success alert-dismissible fade show" role="alert">
                                                <strong>Updated!</strong> Thanks ....
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>`
                    ).insertAfter("#warning");

                    // closing the modal after form update
                    setTimeout(function() {
                            $('#SearchByDate').click();
                            $('#crud-modal').modal('hide');
                        },
                        1000
                    ); // How long you want the delay to be, measured in milliseconds.

                }
                loadOrderAddressFormFunction(order_item_identifier,
                    amazon_order_identifier);
                $("#btn-update-order").attr("disabled", false);
                $('#btn-update-order').html("Update");



            }
        });
    });

    $('#closemodal').click(function() {
        $('#crud-modal').modal('hide');
    });

    function validate() {
        // document.orderAddressForm.btnsave.disabled=false;
        if (document.orderAddressForm.name.value != '' && document.orderAddressForm.phone.value != '') {
            // document.orderAddressForm.btnsave.disabled=false;
        } else {
            // document.orderAddressForm.btnsave.disabled=true;
        }
    }

    $(".datepicker").daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'YYYY-MM-DD',
        },
    });
    $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
            'YYYY-MM-DD'));
    });

    $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    $('#label_search').click(function() {
        selected_date = $('#search_date').val();
        if (selected_date == '') {
            alert('No date Selected Please Select The Date');
            return false;
        } else {
            $.ajax({
                method: 'POST',
                url: "{{ route('lable.search.date') }}",
                data: {
                    'date': selected_date,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                 
                    $('#showTable').removeClass('d-none');
                    $('#label_table').html(response.success);
                },
                error: function(response) {
                     alert('something went wrong');
                }
            });
        }

    });
</script>
@stop