<?php

namespace App\Jobs\catalog;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\API\Catalog;
use App\Services\SP_API\CatalogImport;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class AmazonCatalogImport implements ShouldQueue
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
        $asin_source = $this->payload;
        $type = 4;
        $catalog =  new Catalog();
        $catalog->index($asin_source, $seller_id = NULL, $type);        
    }
}