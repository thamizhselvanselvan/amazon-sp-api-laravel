<?php

namespace App\Http\Controllers\orders;

use Exception;
use Carbon\Carbon;
use RedBeanPHP\R as R;
use AWS\CRT\HTTP\Response;
use Hamcrest\Type\IsObject;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\GetOrderDetails;
use App\Models\Aws_credential;
use SellingPartnerApi\Endpoint;
use AWS\CRT\Auth\AwsCredentials;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Services\Config\ConfigTrait;
use Illuminate\Support\Facades\Auth;
use SellingPartnerApi\Api\OrdersApi;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Artisan;
use Yajra\DataTables\Contracts\DataTable;

use function PHPUnit\Framework\returnSelf;
use App\Services\SP_API\Config\ConfigTrait as ConfigConfigTrait;
use Hamcrest\Arrays\IsArray;

class OrdersListController extends Controller
{
    use ConfigTrait;
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::select('select amazon_order_identifier,purchase_date,last_update_date,order_status,order_total,number_of_items_shipped from orders');
            // dd($data);
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('order_total', function ($order_total) {
                    $price = '';
                    $price = json_decode($order_total->order_total);
                    if (isset($price->Amount) && isset($price->CurrencyCode)) {

                        $price = $price->Amount . ' [' . $price->CurrencyCode . ']';
                    }
                    return $price;
                })
                ->rawColumns(['order_total'])
                ->make(true);;
        }
        return view('orders.listorders.index');
    }

    public function GetOrdersList(Request $request)
    {
        $sellerId = $request->seller_id;

        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            $base_path = base_path();
            $command = "cd $base_path && pms:sellers-orders-import $sellerId > /dev/null &";
            exec($command);
        } else {

            // Log::warning("Export asin command executed local !");
            Artisan::call('pms:sellers-orders-import ' . $sellerId);
        }
        //API will hit here and records will be save into DB
        return redirect()->back();
    }

    public function selectStore(Request $request)
    {
        if ($request->ajax()) {
            $aws_credential = Aws_Credential::with('mws_region')->where('api_type', 1)->get();
            return DataTables::of($aws_credential)
                ->addIndexColumn()
                ->editColumn('region', function ($mws_region) {

                    return $mws_region['mws_region']['region'] . ' [' . $mws_region['mws_region']['region_code'] . ']';
                })
                ->addColumn('action', function ($id) {
                    $action = '<div class="pl-2"><input class="" type="checkbox" value=' . $id['id'] . ' name="options[]" ></div>';
                    return $action;
                })
                ->rawColumns(['region', 'action'])
                ->make(true);;
        }

        return view('orders.listorders.selectstore');
    }

    public function OrderDetails()
    {

        // return 'success';
        return view('orders.ordersDetails.index');
    }
    public function GetOrderDetails()
    {
        $token = 'Atzr|IwEBIG3zt3kKghE3Bl56OEGAxxeodmEzfaMAnMl0PivBlfumR8224Adu9lb33DKLEvHD6OBwdIBkaVlIZ5L2axypPm-LLuKPabvUCmRZ6F6C8KZKBJYS2u1sJVqzMxxoFSs6DTFLMxx8WBVXY395aKUzK3plz3-ttDN-YUGjiKR9-kFhLek1ZdjxwTQkvUdWdfpuDtcnW0veAPS0JUHVwTN39hpwJtPXm98XwD-wEe16n9qoWoak-UvtuML8irbdUdATSA4FLSX08H2V7SFAjdktXEW13v6gBs3xfCYn_w9Y4H29K5i5_vkQyiqj0j1FMK0nmtU';
        $config = new Configuration([
            "lwaClientId" => config('app.aws_sp_api_client_id'),
            "lwaClientSecret" => config('app.aws_sp_api_client_secret'),
            "awsAccessKeyId" => config('app.aws_sp_api_access_key_id'),
            "awsSecretAccessKey" => config('app.aws_sp_api_access_secret_id'),
            "lwaRefreshToken" => $token,
            "roleArn" => config('app.aws_sp_api_role_arn'),
            "endpoint" => Endpoint::EU,
        ]);
        // $host = config('database.connections.web.host');
        // $dbname = config('database.connections.web.database');
        // $port = config('database.connections.web.port');
        // $username = config('database.connections.web.username');
        // $password = config('database.connections.web.password');

        // R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);

        $sellerOrders = DB::select('select seller_identifier,amazon_order_identifier from orders limit 10');
        foreach ($sellerOrders as $sellerOrder) {
            $seller_id =  $sellerOrder->seller_identifier;
            $order_id =  $sellerOrder->amazon_order_identifier;
            GetOrderDetails::dispatch(
                [
                    'order_id' => $order_id,
                    'seller_id' => $seller_id,
                ]
            );

            // $apiInstance = new OrdersApi($config);
            // // $order_id = '405-8984836-8837901'; // string | An Amazon-defined order identifier, in 3-7-7 format.
            // $data_elements = ['buyerInfo', 'shippingAddress']; // string[] | An array of restricted order data elements to retrieve (valid array elements are \"buyerInfo\" and \"shippingAddress\")
            // try {
            //     $results = $apiInstance->getOrder($order_id, $data_elements)->getPayload();
            //     // $results = $apiInstance->getOrderItems($order_id)->getPayload();
            //     $results = json_decode(json_encode($results));
            //     $order_details = R::dispense('orderdetails');
            //     foreach ($results as $resultkey => $result) {

            //         $search = 'Id';
            //         $replaceVal = 'Identifier';
            //         $resultkey = lcfirst($resultkey);

            //         if (substr($resultkey, -2) == 'Id') {

            //             $resultkey = str_replace($search, $replaceVal, $resultkey);
            //         }

            //         if (is_Array($result) || is_object($result)) {

            //             $order_details->$resultkey = (json_encode($result));
            //         } else {

            //             $order_details->$resultkey = $result;
            //         }
            //     }
            //     R::store($order_details);
            // } catch (Exception $e) {
            //     echo 'Exception when calling OrdersApi->getOrder: ', $e->getMessage(), PHP_EOL;
            // }
        }
    }

    public function GetOrderitems()
    {
        $token = 'Atzr|IwEBIG3zt3kKghE3Bl56OEGAxxeodmEzfaMAnMl0PivBlfumR8224Adu9lb33DKLEvHD6OBwdIBkaVlIZ5L2axypPm-LLuKPabvUCmRZ6F6C8KZKBJYS2u1sJVqzMxxoFSs6DTFLMxx8WBVXY395aKUzK3plz3-ttDN-YUGjiKR9-kFhLek1ZdjxwTQkvUdWdfpuDtcnW0veAPS0JUHVwTN39hpwJtPXm98XwD-wEe16n9qoWoak-UvtuML8irbdUdATSA4FLSX08H2V7SFAjdktXEW13v6gBs3xfCYn_w9Y4H29K5i5_vkQyiqj0j1FMK0nmtU';
        $config = new Configuration([
            "lwaClientId" => config('app.aws_sp_api_client_id'),
            "lwaClientSecret" => config('app.aws_sp_api_client_secret'),
            "awsAccessKeyId" => config('app.aws_sp_api_access_key_id'),
            "awsSecretAccessKey" => config('app.aws_sp_api_access_secret_id'),
            "lwaRefreshToken" => $token,
            "roleArn" => config('app.aws_sp_api_role_arn'),
            "endpoint" => Endpoint::EU,
        ]);

        $apiInstance = new OrdersApi($config);
        $order_id = '407-6605984-0453141'; // string | An Amazon-defined order identifier, in 3-7-7 format.

        try {
            $result = $apiInstance->getOrderItems($order_id)->getPayload();

            po($result);
        } catch (Exception $e) {
            echo 'Exception when calling OrdersApi->getOrder: ', $e->getMessage(), PHP_EOL;
        }
    }
}
