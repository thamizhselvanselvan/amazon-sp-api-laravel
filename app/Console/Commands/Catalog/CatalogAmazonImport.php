<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CatalogAmazonImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:catalog-amazon-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Catalog Amazon Import Queue';

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
        $sources = ['in', 'us'];
        foreach($sources as $source){
            $asin_source = [] ;
            $count = 0;
            $queue = 'catalog';
            $class =  'catalog\AmazonCatalogImport';
            $asin_table_name = 'asin_source_'.$source.'s';
            $catalog_table_name = 'catalognew'.$source.'s';
            
            $asins = DB::connection('catalog')->select("SELECT source.asin, source.status, source.user_id 
            FROM $asin_table_name as source
            LEFT JOIN $catalog_table_name as cat
            ON cat.asin = source.asin
            WHERE cat.asin IS NULL
            LIMIT 10");
            
            foreach($asins as $asin)
            {
                if($count == 10){
                    jobDispatchFunc($class, $asin_source, $queue);
                    $asin_source = [];
                    $count = 0;
                }
                $asin_source [] = [
                    'asin' => $asin->asin,
                    'seller_id' => $asin->user_id,
                    'source' => $source,
                ];
                $count++;
            }
            jobDispatchFunc($class, $asin_source, $queue);
        }
    }
}
