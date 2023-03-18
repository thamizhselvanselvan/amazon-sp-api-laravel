<?php

namespace App\Jobs\ShipNTrack\Tracking;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Models\ShipNTrack\ForwarderMaping\IntoAE;
use App\Models\ShipNTrack\ForwarderMaping\IntoKSA;
use App\Services\ShipNTrack\Tracking\CourierTracking;

class CouriersTracking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $payload;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $record = $this->payload;
        Log::alert($record);
        $AramexTracking = new CourierTracking();
        $courierCodeName = ['ss' => 'Smsa', 'am' => 'Aramex', 'bom' => 'Bombino', 'ss_ksa' => 'Smsa'];

        $records = [];
        $awb_no = $record['awbNo'];
        $destination = strtolower($record['destination']);
        Log::alert($awb_no);
        Log::alert($destination);
        if ($destination == 'ae') {

            $records = IntoAE::with(['CourierPartner1', 'CourierPartner2'])
                ->where('awb_number', $awb_no)
                ->get()
                ->toArray();
        } else {
            $records = IntoKSA::with(['CourierPartner1', 'CourierPartner2'])
                ->where('awb_number', $awb_no)
                ->get()
                ->toArray();
        }

        foreach ($records as $record) {

            if ($record['forwarder_1_flag'] == 0 && $record['forwarder_1_awb'] != '') {

                $courierCode = $record['courier_partner1']['courier_code'];
                $accoundId = $record['courier_partner1']['id'];
                $userName = $record['courier_partner1']['key1'];
                $passKey = $record['courier_partner1']['key2'];
                $awb_no = $record['forwarder_1_awb'];

                // po($courierCode);
                // po($userName);
                // po($passKey);
                // po($awb_no);
                $methodName = $courierCodeName[$courierCode] . "API";
                // po($methodName);
                $AramexTracking->$methodName($userName, $passKey, $awb_no, $accoundId);
            }
            if ($record['forwarder_2_flag'] == 0 && $record['forwarder_2_awb'] != '') {

                $courierCode = $record['courier_partner2']['courier_code'];
                $accoundId = $record['courier_partner2']['id'];
                $userName = $record['courier_partner2']['key1'];
                $passKey = $record['courier_partner2']['key2'];
                $awb_no = $record['forwarder_2_awb'];

                // po($courierCode);
                // po($userName);
                // po($passKey);
                // po($awb_no);
                // po($methodName);
                $methodName = $courierCodeName[$courierCode] . "API";
                $AramexTracking->$methodName($userName, $passKey, $awb_no, $accoundId);
            }
            // po($record);
        }
    }
}
