<?php

namespace App\Services\SP_API;

use Exception;
use RedBeanPHP\R as R;
use App\Models\asinMaster;
use App\Models\Aws_credentials;
use SellingPartnerApi\Endpoint;
use Carbon\Laravel\ServiceProvider;
use Illuminate\Support\Facades\Log;
use App\Services\Config\ConfigTrait;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Api\CatalogItemsV0Api as CatalogItemsV0ApiProduct;

class CatalogImport
{
    use ConfigTrait;

    public function amazonCatalogImport()
    {

        Log::warning("warning from handle function");
        $connection = config('app.connection');
        $host = config('app.host');
        $dbname = config('app.database');
        $port = config('app.port');
        $username = config('app.username');
        $password = config('app.password');

        $datas = asinMaster::with(['aws'])->limit(1000)->get();

        try {
            R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);

            foreach ($datas as $data) {

                $asin = $data['asin'];

                $country_code = $data['destination_1'];
                $auth_code = $data['aws']['auth_code'];
                $aws_key = $data['aws']['id'];
                $marketplace_id = $this->marketplace_id($country_code);

                $config = $this->config($aws_key, $country_code, $auth_code);

                $apiInstance = new CatalogItemsV0ApiProduct($config);
                $marketplace_id = $this->marketplace_id($country_code);
                
                try {
                    $result = $apiInstance->getCatalogItem($marketplace_id, $asin);

                    $result = json_decode(json_encode($result));

                    $result = (array)($result->payload->AttributeSets[0]);

                    $productcatalogs = R::dispense('amazon');
                  
                    $value = [];
                    $productcatalogs->asin = $asin;
                    $productcatalogs->destination = $country_code;

                    foreach ($result as $key => $data) {
                        $key = lcfirst($key);
                        if (is_object($data)) {

                            $productcatalogs->{$key} = json_encode($data);
                        } else {
                            $productcatalogs->{$key} = json_encode($data);
                            // $value [][$key] = ($data);
                        }
                    }

                    R::store($productcatalogs);
                    // Log::alert('product catalog saved');
                } catch (Exception $e) {
                    Log::notice($e->getMessage());
                }
            }
        } catch (Exception $e) {
            Log::alert($e->getMessage());
        } finally {

            Log::alert("working");
        }
    }
}