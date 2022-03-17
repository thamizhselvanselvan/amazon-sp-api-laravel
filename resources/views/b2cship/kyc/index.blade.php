@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')
<h1 class="m-0 text-dark"> B2C Ship KYC Details and Status</h1>
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

<div class="container-fluid">
    <div class="row">
           <div class="col-3 ">
             <div class="info-box bg-info text-center">
               <div class="info-box-content">
                 <h2>{{$todayTotalBooking['totalBooking']}}</h2>
                 <h5> Today Total Booking</h5>
               </div>
             </div>
            </div>
          <div class="col-3 ">
             <div class="info-box bg-info text-center">
                <div class="info-box-content">
                 <h2>{{$yesterdayTotalBooking['totalBooking']}}</h2>
                 <h5>Yesterday Total Booking</h5>
                </div>
             </div>
          </div>
          <div class="col-3 ">
             <div class="info-box bg-info text-center">
                <div class="info-box-content">
                <h2>{{$Last7DaysTotalBooking['totalBooking']}}</h2>
                <h5>Last 7 Days Total Booking</h5>
                </div>
             </div>
           </div>
           <div class="col-3 ">
             <div class="info-box bg-info  text-center">
                <div class="info-box-content">
                <h2>{{$Last30DaysTotalBooking['totalBooking']}}</h2>
                <h5>Last 30 Days Total Booking</h5>
                </div>
             </div>
           </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="info-box bg-success text-center">
                <div class="info-box-content">
                <h2>{{$todayTotalBooking['kycApproved']}}</h2>
                    <h5>KYC Approved</h5>
                </div>
            </div>
        </div>

        <div class="col">
        <div class="info-box bg-success text-center">
                <div class="info-box-content">
                 <h2>{{$yesterdayTotalBooking['kycApproved']}}</h2>
                    <h5>KYC Approved</h5>
                </div>
            </div>
        </div>

        <div class="col">
        <div class="info-box bg-success text-center">
                <div class="info-box-content">
                  <h2>{{$Last7DaysTotalBooking['kycApproved']}}</h2>
                    <h5>KYC Approved</h5>
                </div>
            </div>
        </div>
        <div class="col">
          
            <div class="info-box bg-success text-center">
                <div class="info-box-content">
             <h2>{{$Last30DaysTotalBooking['kycApproved']}}</h2>
                   <h5>KYC Approved</h5>
                </div>
            </div>
        </div>
    </div>
  
    <div class="row">
    <div class="col">
            <div class="info-box bg-warning text-center">
                <div class="info-box-content">
                <h2>{{$todayTotalBooking['kycPending']}}</h2>
                    <h5>KYC Pending</h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box bg-warning text-center">
                <div class="info-box-content">
                 <h2>{{$yesterdayTotalBooking['kycPending']}}</h2>
                    <h5>KYC Pending</<h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box bg-warning text-center">
                <div class="info-box-content">
                 <h2>{{$Last7DaysTotalBooking['kycPending']}}</h2>
                   <h5>KYC Pending</<h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box bg-warning text-center">
                <div class="info-box-content">
                  <h2>{{$Last30DaysTotalBooking['kycPending']}}</h2>
                   <h5>KYC Pending</<h5>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
    <div class="col">
            <div class="info-box bg-danger text-center">
                <div class="info-box-content">
                <h2>{{$todayTotalBooking['kycRejected']}}</h2>
                    <h5>KYC Rejected</h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box bg-danger text-center">
                <div class="info-box-content">
                <h2>{{$yesterdayTotalBooking['kycRejected']}}</h2>
                    <h5>KYC Rejected</5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box bg-danger text-center">
                <div class="info-box-content">
                <h2>{{$Last7DaysTotalBooking['kycRejected']}}</h2>
                    <h5>KYC Rejected</h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box bg-danger text-center">
                <div class="info-box-content">
                      <h2>{{$Last30DaysTotalBooking['kycRejected']}}</h2>
                    <h5>KYC Rejected</5>
                </div>
            </div>
        </div>
    </div>
</div>

    @stop

    @section('js')


    @stop