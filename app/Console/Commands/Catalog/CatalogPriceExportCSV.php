<?php

namespace App\Console\Commands\Catalog;

use ZipArchive;
use League\Csv\Writer;
use Illuminate\Console\Command;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CatalogPriceExportCSV extends Command
{
    private $fileNameOffset = 0;
    private $check;
    private $count = 1;
    private $writer;
    private $totalProductCount;
    private $currentCount;
    private $headers_default;
    private $totalFile = [];
    private $country_code;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:catalog-price-export-csv {--country_code=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export catalog Price in CSV accroding to Country code';

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
        $this->country_code = $this->option('country_code');

        $chunk = 10000;

        $exportFilePath = "excel/downloads/catalog_price/$this->country_code/" . $this->country_code . "_CatalogPrice";
        $deleteFilePath = "app/excel/downloads/catalog_price/" . $this->country_code;

        // if (file_exists(storage_path($deleteFilePath))) {
        //     $path = storage_path($deleteFilePath);
        //     $files = (scandir($path));
        //     foreach ($files as $key => $file) {
        //         if ($key > 1) {
        //             unlink($path . '/' . $file);
        //         }
        //     }
        // }

        $record_per_csv = 1000000;
        $chunk = 100000;

        $this->check = $record_per_csv / $chunk;

        if ($this->country_code == 'IN') {

            $headers = ['asin', 'in_price', 'weight', 'uae_sp', 'sg_sp'];
            $csv_header = ['Asin', 'India Price', 'Weight(kg)', 'UAE Selling Price', 'Singapore Selling Price'];
            PricingIn::select($headers)->chunk($chunk, function ($records) use ($exportFilePath, $csv_header, $chunk) {

                $this->CreateCsvFile($csv_header, $records, $exportFilePath);
                //pusher
            });
        } elseif ($this->country_code == 'US') {

            $headers = ['asin', 'weight', 'us_price', 'ind_sp', 'uae_sp', 'sg_sp'];
            $csv_header = ['Asin', 'Weight(kg)', 'US Price', 'India Selling Price', 'UAE Selling Price', 'Singapore Selling Price'];
            PricingUs::select($headers)->chunk($chunk, function ($records) use ($exportFilePath, $csv_header, $chunk) {

                $this->CreateCsvFile($csv_header, $records, $exportFilePath);
                //pusher
            });
        }

        $path = "excel/downloads/catalog_price/" . $this->country_code;
        $path = Storage::path($path);
        $files = (scandir($path));

        $filesArray = [];
        foreach ($files as $key => $file) {
            if ($key > 1) {
                if (str_contains($file, '.mosh')) {
                    $new_file_name = str_replace('.csv.mosh', '.csv', $file);
                    rename($path . '/' . $file, $path . '/' . $new_file_name);
                }
            }
        }
        
        $zip = new ZipArchive;
        $path = "excel/downloads/catalog_price/".$this->country_code."/zip/".$this->country_code."_CatalogPrice.zip";
        $file_path = Storage::path($path);
        
        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }
        
        if($zip->open($file_path, ZipArchive::CREATE) === TRUE)
        {
            foreach($this->totalFile as $key => $value)
            {
                $path = Storage::path('excel/downloads/catalog_price/'.$this->country_code.'/'.$value);
                $relativeNameInZipFile = basename($path);
                $zip->addFile($path, $relativeNameInZipFile);
            }
            $zip->close();
        }
    }
    
    public function CreateCsvFile($csv_header, $records, $exportFilePath)
    {
        if ($this->count == 1) {
            if (!Storage::exists($exportFilePath . $this->fileNameOffset . '.csv.mosh')) {
                Storage::put($exportFilePath . $this->fileNameOffset . '.csv.mosh', '');
            }
            $this->totalFile []= $this->country_code."_CatalogPrice" . $this->fileNameOffset.'.csv';
            Log::notice($this->totalFile);
            $this->writer = Writer::createFromPath(Storage::path($exportFilePath . $this->fileNameOffset . '.csv.mosh'), "w");
            $this->writer->insertOne($csv_header);
        }

        $records = $records->toArray();
        $records = array_map(function ($datas) {
            return (array) $datas;
        }, $records);

        $this->writer->insertall($records);

        if ($this->check == $this->count) {
            $this->fileNameOffset++;
            $this->count = 1;
        } else {
            ++$this->count;
        }

        return true;
        //
    }
}
