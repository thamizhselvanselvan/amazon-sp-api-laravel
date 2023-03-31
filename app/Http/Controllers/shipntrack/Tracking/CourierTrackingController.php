<?php

namespace App\Http\Controllers\shipntrack\Tracking;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;
use App\Models\ShipNTrack\ForwarderMaping\Trackingksa;

class CourierTrackingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = [];
            if ($request->sourceDestination == 'AE') {

                $data = Trackingae::select('awb_number', 'forwarder_1_awb', 'forwarder_2_awb', 'forwarder_3_awb', 'forwarder_4_awb', 'created_at')
                    ->orderBy('awb_number', 'DESC')
                    ->get()
                    ->toArray();
            } elseif ($request->sourceDestination == 'KSA') {
                $data = Trackingksa::select('awb_number', 'forwarder_1_awb', 'forwarder_2_awb', 'forwarder_3_awb', 'forwarder_4_awb', 'created_at')
                    ->orderBy('awb_number', 'DESC')
                    ->get()
                    ->toArray();
            }
            return DataTables::of($data)
                ->editColumn('forwarder1_awb', function ($data) {
                    $forwarder1 = $data['forwarder_1_awb'] ?? 'NA';
                    return $forwarder1;
                })
                ->editColumn('forwarder2_awb', function ($data) {
                    $forwarder2 = $data['forwarder_2_awb'] ?? 'NA';
                    return $forwarder2;
                })
                ->editColumn('forwarder3_awb', function ($data) {
                    $forwarder2 = $data['forwarder_3_awb'] ?? 'NA';
                    return $forwarder2;
                })
                ->editColumn('forwarder4_awb', function ($data) {
                    $forwarder2 = $data['forwarder_4_awb'] ?? 'NA';
                    return $forwarder2;
                })
                ->editColumn('created_date', function ($data) {
                    $created_date = Carbon::parse($data['created_at']);
                    return $created_date;
                })
                ->addColumn('action', function ($data) use ($request) {
                    $action = "<a href='/shipntrack/courier/moredetails/" . $request->sourceDestination . "/" . $data['awb_number'] . "' class='' target='_blank'>More Details</a>";
                    return $action;
                })
                ->rawColumns(['forwarder1_awb', 'forwarder2_awb', 'forwarder3_awb', 'forwarder4_awb', 'created_date', 'action'])
                ->make(true);
        }
        return view('shipntrack.Smsa.index');
    }

    public function PacketMoreDetails($sourceDestination, $awbNo)
    {
        $OrderByColunm = [
            'SMSA' => 'date',
            'Aramex' => 'update_date_time',
            'Bombino' => 'action_date'

        ];

        $selectColumns = [
            'SMSA' => [
                'date',
                'activity',
                'location',
            ],
            'Aramex' => [
                'update_date_time',
                'update_description',
                'update_location',
            ],
            'Bombino' => [
                'action_date',
                'action_time',
                'event_detail',
                'location'
            ],

        ];
        $result = [];
        if ($sourceDestination == 'AE') {

            $result = Trackingae::with([
                'CourierPartner1.courier_names',
                'CourierPartner2.courier_names',
                'CourierPartner3.courier_names',
                'CourierPartner4.courier_names'
            ])
                ->where('awb_number', $awbNo)
                ->get()
                ->toArray();
        } elseif ($sourceDestination == 'KSA') {

            $result = Trackingksa::with([
                'CourierPartner1.courier_names',
                'CourierPartner2.courier_names',
                'CourierPartner3.courier_names',
                'CourierPartner4.courier_names'
            ])
                ->where('awb_number', $awbNo)
                ->get()
                ->toArray();
        }

        $forwarder_details = [
            'consignor' => $result[0]['consignor'],
            'consignee' => $result[0]['consignee'],
        ];

        $forwarder1_data = [];
        if (isset($result[0]['forwarder_1_awb'])) {

            $awb_no = $result[0]['forwarder_1_awb'];
            $courier_name = $result[0]['courier_partner1']['courier_names']['courier_name'];
            $table = table_model_change(model_path: 'CourierTracking', model_name: ucwords(strtolower($courier_name)) . 'Tracking', table_name: strtolower($courier_name) . '_trackings');

            $forwarder1_data = $table->select($selectColumns[$courier_name])
                ->where('awbno', $awb_no)
                ->orderBy($OrderByColunm[$courier_name], 'DESC')
                ->get()
                ->toArray();
        }

        $forwarder2_data = [];
        if (isset($result[0]['forwarder_2_awb'])) {

            $awb_no = $result[0]['forwarder_2_awb'];
            $courier_name = $result[0]['courier_partner2']['courier_names']['courier_name'];
            $table = table_model_change(model_path: 'CourierTracking', model_name: ucwords(strtolower($courier_name)) . 'Tracking', table_name: strtolower($courier_name) . '_trackings');

            $forwarder2_data = $table->select($selectColumns[$courier_name])
                ->where('awbno', $awb_no)
                ->orderBy($OrderByColunm[$courier_name], 'DESC')
                ->get()
                ->toArray();
        }
        $forwarder3_data = [];
        if (isset($result[0]['forwarder_3_awb'])) {

            $awb_no = $result[0]['forwarder_3_awb'];
            $courier_name = $result[0]['courier_partner3']['courier_names']['courier_name'];
            $table = table_model_change(model_path: 'CourierTracking', model_name: ucwords(strtolower($courier_name)) . 'Tracking', table_name: strtolower($courier_name) . '_trackings');

            $forwarder3_data = $table->select($selectColumns[$courier_name])
                ->where('awbno', $awb_no)
                ->orderBy($OrderByColunm[$courier_name], 'DESC')
                ->get()
                ->toArray();
        }

        $records = [...$forwarder1_data, ...$forwarder2_data, ...$forwarder3_data];

        return view('shipntrack.Smsa.packetDetails', compact('forwarder_details', 'records'));
    }

    public function getDetails()
    {
        commandExecFunc('mosh:courier-tracking');
        return Redirect::back()->with('success', 'Fetching details please wait..');
    }

    public function uploadAwb()
    {
        return view('shipntrack.Smsa.upload');
    }

    public function GetTrackingDetails(Request $request)
    {
        $request->validate([
            'smsa_awbNo' => 'required|min:10',
        ]);

        $tracking_id = $request->smsa_awbNo;

        $datas = preg_split('/[\r\n| |:|,]/', $tracking_id, -1, PREG_SPLIT_NO_EMPTY);
        $datas = array_unique($datas);

        $count = 0;
        $awbNo_array = [];

        $class = 'ShipNTrack\\SMSA\\SmsaGetTracking';
        $queue_type = 'tracking';

        foreach ($datas as $awbNo) {
            if ($count == 5) {

                jobDispatchFunc(class: $class, parameters: $awbNo_array, queue_type: $queue_type);
                $awbNo_array = [];
                $count = 0;
            }
            $awbNo_array[] = $awbNo;
            $count++;
        }

        jobDispatchFunc(class: $class, parameters: $awbNo_array, queue_type: $queue_type);
        return redirect()->intended('/shipntrack/smsa')->with('success', 'Tracking Details Saved');
    }
}