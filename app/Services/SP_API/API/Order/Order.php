<?php

namespace App\Services\SP_API\API\Order;

use App\Jobs\Orders\GetOrderItem;
use Exception;
use RedBeanPHP\R;
use Carbon\Carbon;
use App\Models\Aws_credential;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use SellingPartnerApi\Api\OrdersApi;
use SellingPartnerApi\Configuration;
use App\Services\SP_API\Config\ConfigTrait;
use App\Models\order\OrderSellerCredentials;


class Order
{
    use ConfigTrait;

    public function SelectedSellerOrder($awsId, $awsCountryCode, $awsAuth_code)
    {
        $seller_id = $awsId;

        $host = config('database.connections.order.host');
        $dbname = config('database.connections.order.database');
        $port = config('database.connections.order.port');
        $username = config('database.connections.order.username');
        $password = config('database.connections.order.password');

        if (!R::testConnection('order', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password)) {
            R::addDatabase('order', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
            R::selectDatabase('order');
        }

        $config = $this->config($awsId, $awsCountryCode, $awsAuth_code);
        $marketplace_ids = $this->marketplace_id($awsCountryCode);
        $marketplace_ids = [$marketplace_ids];

        $apiInstance = new OrdersApi($config);
        $startTime = Carbon::now()->subHours(2)->toISOString();
        // $startTime = Carbon::now()->subDays(5)->toISOString();

        // echo $startTime;
        $createdAfter = $startTime;
        $lastUpdatedBefore = now()->toISOString();
        $max_results_per_page = 100;
        $next_token = NULL;

        try {
            next_token_exist:
            $results = $apiInstance->getOrders($marketplace_ids, $createdAfter, $created_before = null, $last_updated_after = null, $last_updated_before = null, $order_statuses = null, $fulfillment_channels = null, $payment_methods = null, $buyer_email = null, $seller_order_id = null, $max_results_per_page, $easy_ship_shipment_statuses = null, $next_token, $amazon_order_ids = null, $actual_fulfillment_supply_source_id = null, $is_ispu = null, $store_chain_store_id = null, $data_elements = null)->getPayload();
            $next_token = $results['next_token'];
            $this->OrderDataFormating($results, $awsCountryCode, $awsId);

            if (isset($next_token)) {
                goto next_token_exist;
            }
            $orders = '';
            $amazon_order_id = '';
        } catch (Exception $e) {

            Log::warning('Exception when calling OrdersApi->getOrders: '. $e->getMessage());
        }
    }


    public function OrderDataFormating($results, $awsCountryCode, $awsId)
    {
        $result_data = $results->getOrders();
        $result_data = json_decode(json_encode($result_data));
        $count = 0;

        $delay = 0;
        $delay_count = 18;

        foreach ($result_data as $resultkey => $result) {

            $orders = R::dispense('orders');
            $amazon_order_details = [];
            $orders->our_seller_identifier = $awsId;
            $orders->country = $awsCountryCode;
            $amazon_order_id = '';
            foreach ((array)$result as $detailsKey => $details) {


                $detailsKey = lcfirst($detailsKey);
                if (is_Object($details)) {

                    $amazon_order_details[$detailsKey] = json_encode($details);
                    $orders->{$detailsKey} = json_encode($details);
                } else if (is_array($details)) {

                    $amazon_order_details[$detailsKey] = json_encode($details);
                    $orders->{$detailsKey} = json_encode($details);
                } else {

                    $id = substr($detailsKey, -2);
                    if ($id == 'Id') {
                        $detailsKey = str_replace("Id", "Identifier", $detailsKey);
                    }
                    if ($detailsKey == 'amazonOrderIdentifier') {

                        $amazon_order_id = $details;
                        $amazon_order_details['amazon_order_identifier'] = $details;
                        $orders->amazon_order_identifier = $details;
                    } else {

                        $amazon_order_details[$detailsKey] = (string)$details;
                        $orders->{$detailsKey} = (string)$details;
                    }
                }
            }

            //$amazon_order_id = '407-0297568-739477566';.01

            $data = DB::connection('order')->select("select id, amazon_order_identifier from orders where amazon_order_identifier = '$amazon_order_id'");
            //   $data = [];
            if (array_key_exists(0, $data)) {
                $count++;
                $dataCheck = 1;
                $id = $data[0]->id;
                $update_orders = R::load('orders', $id);
                $update_orders->our_seller_identifier = $awsId;
                foreach ($amazon_order_details as $key => $value) {
                    $update_orders->{$key} = $value;
                }
                $update_orders->updatedat = now();
                R::store($update_orders);
            } else {
                //call orderitem details jobs
                $orders->order_item = '0';
                $orders->updatedat = now();
                $orders->createdat = now();
                // dd($orders);
                
                R::store($orders);

                if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
                    GetOrderItem::dispatch(
                        [
                            'order_id' => $amazon_order_id,
                            'aws_id' => $awsId,
                            'country_code' => $awsCountryCode,
                           
                        ]
                    )->onConnection('redis')->onQueue('order')->delay($delay);

                    $delay += $delay_count;
                } else {

                    GetOrderItem::dispatch(
                        [
                            'order_id' => $amazon_order_id,
                            'aws_id' => $awsId,
                            'country_code' => $awsCountryCode,
                         
                        ]
                    );
                }
            }
        }
        // return true;
    }
}