<?php

namespace App\Console\Commands\BusinessAPI;

use Illuminate\Console\Command;
use NunoMaduro\Collision\Writer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class access_token_generate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:access_token_generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates the access code using Referesh Token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::alert("Acess token generation started");
        $postData = array(
            "grant_type" => "refresh_token",
            "client_id" =>  "amzn1.application-oa2-client.6c64a78c8f214ae1999ba6725aa68bd5",
            "client_secret" => "80b1db8f2e3ae4b755bd50a0bcc21228694381e6a35b178efdb43799ccedd1ae",
            "refresh_token" =>  "Atzr|IwEBIGBnur8ckY5T1BPQTCvdM-LDHEQ1rqpBrKNAy44n_6HQKhz2DstKC0hOFUkWowyUN64k99Fj6BVJCR0nXTXn_MD6dLaoAHtsKQW6_VDStqyR8FImcHm94A6SLuGukK6qHNF0-4c9hY3nx03jBHQ9K4TOLg55O-vDTkr6T6WCn4q0hcJ05e4324qZdOBvoAzaJb9NFQkQyjX1ken_o9Wf55aZzbbgPcRmxqKy35o9k2HdcLFFnr9Qj737MMXQQ0AUT8f5YhMg7cNF6DM2VIX43wK3WMG6JwGjkszmL-4TWKbv4bRjRZXHLs1WkgUmwMGyH_0Lr6bgtQbvj76m9WQGg11H",
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.amazon.com/auth/O2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        $response = curl_exec($ch);
        curl_close($ch);
        $r = json_decode($response);
        $token =  $r->access_token;

        $file_path = 'Business\token';
        if (!Storage::exists($file_path)) {
            Storage::put($file_path, '');
        }
        Storage::put($file_path, $token);
        Log::alert("Acess token generated Successfully");
    }
}
