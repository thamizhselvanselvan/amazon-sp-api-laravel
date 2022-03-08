<?php

use RedBeanPHP\R;
use App\Models\User;
use App\Models\Mws_region;
use Maatwebsite\Excel\Row;
use SellingPartnerApi\Endpoint;
use App\Models\universalTextile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use SellingPartnerApi\Api\ProductPricingApi;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('/');

Auth::routes();

Route::get('login', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('login');
Route::get('home', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('home');

// Route::group(['middleware' => ['role:Admin', 'auth'], 'prefix' => 'admin'],function(){

// Route::get('dashboard', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('admin.dashboard');

// });

Route::resource('admin/mws_regions', 'Admin\RegionController');
Route::get('admin/credentials', 'Admin\CredentialsController@index');
Route::get('admin/currencys', 'Admin\CurrencyController@index');
Route::get('admin/rolespermissions', 'Admin\RolesPermissionsController@index');

Route::get('asin-master', 'AsinMasterController@index');
Route::get('add-asin', 'AsinMasterController@addAsin');
Route::get('import-bulk-asin', 'AsinMasterController@importBulkAsin');
Route::get('export-asin', 'AsinMasterController@exportAsinToCSV');
Route::post('add-bulk-asin', 'AsinMasterController@addBulkAsin');
Route::get('asinMaster_download', 'filedownloads\FileDownloadsController@download_asin_master')->name('download.asinMaster');

Route::resource('textiles', 'textilesController');
Route::post('import-csv', 'textilesController@importTextiles')->name('import.csv');
Route::get('export_to_csv', 'textilesController@exportTextilesToCSV')->name('export.csv');

Route::get('file_downloads', 'filedownloads\FileDownloadsController@filedownloads')->name('file.downloads');
Route::get('universalTextiles_download', 'filedownloads\FileDownloadsController@download_universalTextiles')->name('download.universalTextiles');

Route::get('product/amazon_com', 'product\productController@index')->name('product.amazon_com');
Route::get('product/fetch_from_amazon', 'product\productController@fetchFromAmazon')->name('product.fetch.amazon');
Route::get('product/getPricing', 'product\productController@amazonGetPricing')->name('amazon.getPricing');

Route::get('other-product/amazon_com', 'otherProduct\anotherAmazonProduct@index')->name('product.amazon_com');
Route::get('other-product/export', 'otherProduct\anotherAmazonProduct@exportOtherProduct')->name('export.other-product');
Route::get('other-product/download/{id}', 'filedownloads\FileDownloadsController@download_other_product')->name('download.other-product');

Route::get('path', function () {

     $file_path = "excel/downloads/universalTextilesExport.csv";
     echo Storage::path($file_path);
     echo "<hr>";
     echo "Base Path:- ";
     echo base_path();

     echo "<hr>";
     echo 'saving path :- ';
     $file_path = "excel\\downloads\\universalTextilesExport.csv";
     echo Storage::path($file_path);

     //echo Str;
});


Route::resource('/tests', 'TestController');
Route::get('/test', function () {

     $path = 'universalTextilesImport/textiles.csv';

     return Storage::url($path);

     return ('downloaded done');
});

Route::get('/remove', function () {

     universalTextile::truncate();
});


Route::get('/amazon_count', 'TestController@index');

Route::get('/asin/{asin}/{code}', 'TestController@getASIN');

Route::get('/pdo', function () {

     $host = config('app.host');
     $dbname = config('app.database');
     $port = config('app.port');
     $username = config('app.username');
     $password = config('app.password');


     try {
          po($host);
          po($dbname);
          po($port);
          po($username);
          po($password);
          //$db = new PDO('mysql: host=' . $host . '; dbname=' . $dbname . ';port=' . $port, $username, $password);
          R::setup('mysql: host='.$host.'; dbname='.$dbname.';port='.$port, $username, $password);
     } catch (PDOException $e) {
          echo $e->getmessage();
     } finally {
          echo 'working';
     }
});


Route::get("pricing", function() {

     $india_token="Atzr|IwEBIJbccmvWhc6q6XrigE6ja7nyYj962XdxoK8AHhgYvfi-WKo3MsrbTSLWFo79My_xmmT48DSVh2e_6w8nxgaeza9XZ9HtNnk7l4Rl_nWhhO6xzEdfIfU7Ev4hktjvU8CjMvYnRn_Cw5JveEqZSggp961Sg7CoBEDpwXZbAE3SYXSdeNxfP2Nu84y2ZzlsP3CNZqcTvXMWflLk1qqY6ittwlGAXpL0BwGxPCBRmjbXOy5xsZqwCPAQhW6l9AJtLPhwOlSSDjcxxvCTH9-LEPSWHLRP1wV3fRgosOlCsQgmuET0pm5SO7FVJTRWux8h2k5hnnM";
     $usa_token="Atzr|IwEBIJRFy0Xkal83r_y4S7sGsIafj2TGvwfQc_rppZlk9UzT6EuqEn9SaHmQfNbmEhOtk8Z6Dynk43x15TpyS3c2GuybzctGToAmjwGxiWXCwo2M3eQvOWfVdicOaF1wkivMAVH8lO8Qt3LtvCNjk5yiRsY5zPTJpShWRqiZ570lpcVb8D1HghZRQCaluoGkuVNOKZquXBF4KSwLur6duoDrUw5ybAIECAMclRbNtUulG9X2T902Wg6dKBSKq_3R-cNbOQ2Ld3-iSguanUI5SsSJOjdVJRpzuTkcWL2GcdFCSlp6NHnRV-2NLCcvZi3ZLtkonIg";
     $config = new Configuration([
          "lwaClientId" => "amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf",
          "lwaClientSecret" => "5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765",
          "lwaRefreshToken" => $usa_token,
          "awsAccessKeyId" => "AKIAZTIHMXYBD5SRG5IZ",
          "awsSecretAccessKey" => "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",
          "endpoint" => Endpoint::NA,  // or another endpoint from lib/Endpoints.php
          "roleArn" => 'arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role'
      ]);
      
      $apiInstance = new ProductPricingApi($config);
      $marketplace_id_india = 'A21TJRUUN4KGV'; // string | A marketplace identifier. Specifies the marketplace for which prices are returned.
      $marketplace_id_usa = 'ATVPDKIKX0DER'; // string | A marketplace identifier. Specifies the marketplace for which prices are returned.
      $item_type = 'Asin'; // string | Indicates whether ASIN values or seller SKU values are used to identify items. If you specify Asin, the information in the response will be dependent on the list of Asins you provide in the Asins parameter. If you specify Sku, the information in the response will be dependent on the list of Skus you provide in the Skus parameter.
      $asins = ['B0000632EN']; // string[] | A list of up to twenty Amazon Standard Identification Number (ASIN) values used to identify items in the given marketplace.
      $skus = array(); // string[] | A list of up to twenty seller SKU values used to identify items in the given marketplace.
      $item_condition = 'New'; // string | Filters the offer listings based on item condition. Possible values: New, Used, Collectible, Refurbished, Club.
      $offer_type = 'B2C'; // string | Indicates whether to request pricing information for the seller's B2C or B2B offers. Default is B2C.
      

      print_r($asins);
      
      try {
          $result = $apiInstance->getCompetitivePricing($marketplace_id_usa, $item_type, $asins)->getPayload();
          po($result);
      } catch (Exception $e) {
          echo 'Exception when calling ProductPricingApi->getPricing: ', $e->getMessage(), PHP_EOL;
      }

});

include_route_files(__DIR__ . '/pms/');
