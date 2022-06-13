@extends('adminlte::page')
@section('title', 'Invoice')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Invoice Management</h1>
    <h2 class="mb-4 text-right col">
        <a href="upload">
            <x-adminlte-button label="Upload Invoice Excel" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
        </a>
        <!-- <a href="Export/view">
            <x-adminlte-button label="Download Invoice PDF" theme="primary" icon="fas fa-file-download" class="btn-sm" />
        </a> -->
        <!-- <a href="">
            <x-adminlte-button label="Download All file" id="checkall" theme="primary" icon="fas fa-file-download" />
        </a> -->
        <a href="download-all"> 
            <x-adminlte-button label="Download All" id='download_pdf' theme="primary" icon="fas fa-check-circle" class="btn-sm" />
        </a>
    </h2>
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

<table class="table table-bordered yajra-datatable table-striped">
    <thead>
        <tr class="text-bold bg-info">
            <td>S/N</td> 
            <td>Invoice No.</td>
            <td>Invoice Date</td>
            <td>Channel</td>
            <td>Shipped By</td>
            <td>Awb No</td>
            <td>Arn NO.</td>
            <td>Hsn Code</td>
            <td>Quantity</td>
            <td>Product Price</td>
            <td>Action</td>
        </tr>
    </thead>
    <tbody>
    </tbody>
    

</table>
@stop

@section('js')
<script>

    let yajra_table = $('.yajra-datatable').DataTable({

    processing: true,
    serverSide: true,
    ajax: "{{ url('/invoice/manage') }}",
    columns: [{
        data: 'DT_RowIndex',
        name: 'DT_RowIndex',
        orderable: false,
        searchable: false
        },
        {
            data: 'invoice_no',
            name: 'invoice_no'
        },
        {
            data: 'invoice_date',
            name: 'invoice_date',
            orderable: false,
        },
        {
            data: 'channel',
            name: 'channel'
        },
        {
            data: 'shipped_by',
            name: 'shipped_by',
        },

        {
            data: 'awb_no',
            name: 'awb_no',
        },
        {
            data: 'arn_no',
            name: 'arn_no'
        },
        {
            data: 'hsn_code',
            name: 'hsn_code'
        },
        {
            data: 'qty',
            name: 'qty'
        },
        {
            data: 'product_price',
            name: 'product_price'
        },
        {
            data: 'action',
            name: 'action'
        },

    ],

    });

    // $('#download_pdf').click( function() {

    //     let id = '';
    //     let count = 0;
    //     $("input[name='options[]']:checked").each(function() {
    //         if (count == 0) {
    //             id += $(this).val();
    //         } else {
    //             id += '-' + $(this).val();
    //             alert(id);
    //         }
    //         count++;
        
    //     });
    //     window.location.href = '/invoice/download-all/'+id;

    // });

    $(document).ready(function(){
      $('#Export_to_pdf').click(function(e){
         e.preventDefault();
         var url = $(location).attr('href');
         var id = $('#pid').val();
         var all = $('#all').val();

         alert(working);
         // alert(alert);
         // alert(url);

            $.ajax({
                method: 'POST',
                url: "{{ url('/invoice/export-pdf')}}",
                data:{ 
                'url':url,
                'id':id,
                'total' : all,
                "_token": "{{ csrf_token() }}",
                },
                success: function(response) {

                window.location.href = '/invoice/download/'+id;
                alert('Export pdf successfully');
                }
            });
        });
    });

</script>
@stop