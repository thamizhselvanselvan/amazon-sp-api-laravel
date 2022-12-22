@extends('adminlte::page')

@section('title', 'Maintenance Mode')

@section('css')

@stop
@section('content_header')
    <div class="row">
        <div class="col">

            <h1 class="m-0 text-dark"><b>System Maintenance</b></h1>
        </div>
    </div>
@stop

@section('content')

    <div class="custom-control custom-switch">

        <input type="checkbox" class="custom-control-input btn-lg" id="maintenance_mode" name="mode">
        <label class="custom-control-label" for="maintenance_mode">Maintenance Mode On/Off</label>

    </div>

@stop

@section('js')
    <script text="javascript">
        $('#maintenance_mode').click(function() {
            mode = $(this).val();
            // alert('working');
            if (mode == 'on') {
                $(this).val('off');
            } else {
                $(this).val('on');
            }
            // alert(mode);

            $.ajax({
                method: 'POST',
                url: "{{ route('maintenance.mode.on.off') }}",
                data: {
                    'mode': mode,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    console.log(response);
                }
            });
        });
    </script>
@stop
