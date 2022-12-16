<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\Log;
use App\Services\Catalog\BuyBoxPriceImport;

class CatalogPriceImportIN extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:Catalog-price-import-bb-in';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import catalog price form bb table for IN';

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
        //Process Management start
        $process_manage = [
            'module'             => 'Catalog_price_bb_in',
            'description'        => 'Import catalog IN price from bb table',
            'command_name'       => 'mosh:Catalog-price-import-bb-in',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];
        // $pm_id = ProcessManagementCreate($process_manage['command_name']);
        //Process Management end
        // $source = [
        //     'US' => 40,
        //     'IN' => 39
        // ];

        $country_code = 'IN';
        $seller_id = '39';
        $limit = 1000;

        $buy_box_price = new BuyBoxPriceImport();
        $buy_box_price->fetchPriceFromBB($country_code, $seller_id, $limit);

        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
    }
}
