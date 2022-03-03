<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\DB;
use App\Services\Config\ConfigTrait;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Api\CatalogApi;
use SellingPartnerApi\Api\CatalogItemsV0Api;
use SellingPartnerApi\Api\ProductPricingApi;

class TestController extends Controller
{   use ConfigTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $amazonCoutn = DB::select('select count(*) from amazon ');
        print_r($amazonCoutn);
        echo "<hr>";
        $amazonData = DB::select('select * from amazon');
        foreach ($amazonData as $data) {

            print_r(json_decode(json_encode($data)));
            echo "<hr>";
        }
    }

    public function getASIN($asin, $country_code){

        
        $asins = array($asin);
        $token ='';
        $marketplace = '';
        $endpoint ='';

    switch ($country_code){
        case 'US':
        case 'us':
            //us Token
            $token="Atzr|IwEBIJRFy0Xkal83r_y4S7sGsIafj2TGvwfQc_rppZlk9UzT6EuqEn9SaHmQfNbmEhOtk8Z6Dynk43x15TpyS3c2GuybzctGToAmjwGxiWXCwo2M3eQvOWfVdicOaF1wkivMAVH8lO8Qt3LtvCNjk5yiRsY5zPTJpShWRqiZ570lpcVb8D1HghZRQCaluoGkuVNOKZquXBF4KSwLur6duoDrUw5ybAIECAMclRbNtUulG9X2T902Wg6dKBSKq_3R-cNbOQ2Ld3-iSguanUI5SsSJOjdVJRpzuTkcWL2GcdFCSlp6NHnRV-2NLCcvZi3ZLtkonIg";
            $marketplace = 'ATVPDKIKX0DER'; // string | A marketplace identifier. Specifies the marketplace for which prices are returned.
            $endpoint = Endpoint::NA;
            break;

        case 'IN' :
        case 'in' :
            //india Token
            $token="Atzr|IwEBIJbccmvWhc6q6XrigE6ja7nyYj962XdxoK8AHhgYvfi-WKo3MsrbTSLWFo79My_xmmT48DSVh2e_6w8nxgaeza9XZ9HtNnk7l4Rl_nWhhO6xzEdfIfU7Ev4hktjvU8CjMvYnRn_Cw5JveEqZSggp961Sg7CoBEDpwXZbAE3SYXSdeNxfP2Nu84y2ZzlsP3CNZqcTvXMWflLk1qqY6ittwlGAXpL0BwGxPCBRmjbXOy5xsZqwCPAQhW6l9AJtLPhwOlSSDjcxxvCTH9-LEPSWHLRP1wV3fRgosOlCsQgmuET0pm5SO7FVJTRWux8h2k5hnnM";
            $marketplace = 'A21TJRUUN4KGV'; // string | A marketplace identifier. Specifies the marketplace for which prices are returned. 
            $endpoint = Endpoint::EU;
        break;

        case 'SA' :
        case 'sa' :
           // Saudi Arabia
            $token = '';
            $marketplace = 'A17E79C6D8DWNP';
            $endpoint = Endpoint::EU;
        break;

        case 'AE' :
        case 'ae' :
            //UAE
            $token = 'Atzr|IwEBIHB8x1yx3bRdnjOICk1qxFMYPczPiS9NGCHm6M-f6SLwHbzaehUZz7mWKRNddG5LFo4ZB00DdONe3u8udOBOR6X6GtJug36YXJeFMIvU7t-2-DJMZ-1PjOBi6U6ubuaAOa2jottylPzVsvKpht6DbTu3rvKtziVq338I8wUV2PnMRPCfc6cM8_9PAQLNVGBBCHiRevHxh9_gsjKCNFEexQD3gQrZPTE5yXnhWkPRv_dSmRdty1P1gmDDK6G8OyotfabU8C_L9ujIIVz13m6Go9eCalMkO_EVtHwTDDICusjxiA26JRbk7qRmPzNL7iiCocY';
            $marketplace = 'A2VIGQ35RCS4UG';
            $endpoint = Endpoint::EU;
        break;
    }
     
     //$usa_token="Atzr|IwEBIJRFy0Xkal83r_y4S7sGsIafj2TGvwfQc_rppZlk9UzT6EuqEn9SaHmQfNbmEhOtk8Z6Dynk43x15TpyS3c2GuybzctGToAmjwGxiWXCwo2M3eQvOWfVdicOaF1wkivMAVH8lO8Qt3LtvCNjk5yiRsY5zPTJpShWRqiZ570lpcVb8D1HghZRQCaluoGkuVNOKZquXBF4KSwLur6duoDrUw5ybAIECAMclRbNtUulG9X2T902Wg6dKBSKq_3R-cNbOQ2Ld3-iSguanUI5SsSJOjdVJRpzuTkcWL2GcdFCSlp6NHnRV-2NLCcvZi3ZLtkonIg";
     $config = new Configuration([
          "lwaClientId" => "amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf",
          "lwaClientSecret" => "5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765",
          "lwaRefreshToken" => $token,
          "awsAccessKeyId" => "AKIAZTIHMXYBD5SRG5IZ",
          "awsSecretAccessKey" => "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",
          "endpoint" => $endpoint,  // or another endpoint from lib/Endpoints.php
          "roleArn" => 'arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role'
      ]);
      
      $apiInstance = new ProductPricingApi($config);
      $item_type = 'Asin'; // string | Indicates whether ASIN values or seller SKU values are used to identify items. If you specify Asin, the information in the response will be dependent on the list of Asins you provide in the Asins parameter. If you specify Sku, the information in the response will be dependent on the list of Skus you provide in the Skus parameter.
      $skus = array(); // string[] | A list of up to twenty seller SKU values used to identify items in the given marketplace.
      $item_condition = 'New'; // string | Filters the offer listings based on item condition. Possible values: New, Used, Collectible, Refurbished, Club.
      $offer_type = 'B2C'; // string | Indicates whether to request pricing information for the seller's B2C or B2B offers. Default is B2C.
      
      echo 'Product Pricing Api / getCompetitivePricing';
      echo"<hr>";
      echo "<pre>";
      try {
          $result = $apiInstance->getCompetitivePricing($marketplace, $item_type, $asins)->getPayload();
          $result = json_decode(json_encode($result));
          print_r($result);
          $pricing = $result[0]->Product->CompetitivePricing->CompetitivePrices[0]->Price->LandedPrice;
            print_r($pricing->CurrencyCode);
            print_r($pricing->Amount);
        //   $result = (array)($result->payload->AttributeSets[0]);
        } catch (Exception $e) {
            echo 'Exception when calling ProductPricingApi->getCompetitivePricing: ', $e->getMessage(), PHP_EOL;
        }
        
        echo"<hr>";
      echo 'Product Pricing Api / getItemOffers';
      echo"<hr>";
      echo "<pre>";
    //   try {
    //     $result = $apiInstance->getItemOffers($marketplace, $item_condition, $asin);
    //     //   $result = json_decode(json_encode($result));
    //       $result = ($result);
    //      print_r($result);
    //   } catch (Exception $e) {
    //       echo 'Exception when calling ProductPricingApi->getItemOffers: ', $e->getMessage(), PHP_EOL;
    //   } 
      echo 'Product Pricing Api / getListingOffers';
    //   try {
    //     $result = $apiInstance->getListingOffers($marketplace, $item_condition, $seller_sku);
    //       $result = json_decode(json_encode($result));
    //      po($result);
    //   } catch (Exception $e) {
    //       echo 'Exception when calling ProductPricingApi->getPricing: ', $e->getMessage(), PHP_EOL;
    //   } 
    echo"<hr>";
      echo 'Product Pricing Api / getPricing';
      echo"<hr>";
      $asins = ['B00005C2M2', 'B00006IDK9'];
      try {
        $result = $apiInstance->getPricing($marketplace, $item_type, $asins);
        po($result);
        //   $result = json_decode(json_encode($result));
        //  po($result);
      } catch (Exception $e) {
          echo 'Exception when calling ProductPricingApi->getPricing: ', $e->getMessage(), PHP_EOL;
      } 


    echo '<hr>';
    
    $apiInstance = new CatalogApi($config);
    echo 'Catalog Items API v2020-12-01/ getCatalogItem';
    echo"<hr>";


    // try {
    //     $result = $apiInstance->getCatalogItem($asin, $marketplace, 'attributes');
    //     po($result);
    // } catch (Exception $e) {
    //     echo 'Exception when calling CatalogApi->getCatalogItem: ', $e->getMessage(), PHP_EOL;
    // }
    
    
    
    echo '<hr>';
    $apiInstance = new CatalogItemsV0Api($config);
    echo 'Catalog Items API v0 / getCatalogItem';
    echo"<hr>";
    
    // try {
    //     $result = $apiInstance->getCatalogItem($marketplace, $asin);
    //     po($result);
    // } catch (Exception $e) {
    //     echo 'Exception when calling CatalogApi V0->getCatalogItem: ', $e->getMessage(), PHP_EOL;
    // }

    echo"<hr>";
    echo 'Catalog Items API v0 / listCatalogCategories';
    echo"<hr>";
    // try {
    //     $result = $apiInstance->listCatalogCategories($marketplace, $asin);
    //     po($result);
    // } catch (Exception $e) {
    //     echo 'Exception when calling CatalogItemsV0Api->listCatalogCategories: ', $e->getMessage(), PHP_EOL;
    // }
    echo"<hr>";
    echo 'Catalog Items API v0 / listCatalogItems';
    echo"<hr>";
//     try {
//     $result = $apiInstance->listCatalogItems($marketplace);
//     print_r($result);
// } catch (Exception $e) {
//     echo 'Exception when calling CatalogItemsV0Api->listCatalogItems: ', $e->getMessage(), PHP_EOL;
// }
}

    
}
