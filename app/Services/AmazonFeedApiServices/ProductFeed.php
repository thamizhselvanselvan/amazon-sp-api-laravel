<?php

namespace App\Services\PMS_SP_API\API;

use Exception;
use SellingPartnerApi\Document;
use SellingPartnerApi\FeedType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\SP_API\Config\ConfigTrait;
use SellingPartnerApi\Api\FeedsV20210630Api as FeedsApi;
use SellingPartnerApi\Model\FeedsV20210630\CreateFeedSpecification;
use SellingPartnerApi\Model\FeedsV20210630\CreateFeedDocumentSpecification;

class ProductFeed
{

    use ConfigTrait;

    public function createFeedDocument($aws_key, $country_code, $feedLists, $merchant_id, $currency_code, $marketplace_ids, $available = false)
    {

        $apiInstance = new FeedsApi($this->config($aws_key, $country_code));
        $feedType = ($available) ? FeedType::POST_INVENTORY_AVAILABILITY_DATA : FeedType::POST_PRODUCT_PRICING_DATA;

        //$body->setContentType('text/xml; charset=UTF8');
        try {
            $createFeedDocSpec  = new CreateFeedDocumentSpecification(['content_type' => $feedType['contentType']]); // \SellingPartnerApi\Model\Feeds\CreateFeedDocumentSpecification
            $feedDocumentInfo = $apiInstance->createFeedDocument($createFeedDocSpec);
            $feedDocumentId = $feedDocumentInfo->getFeedDocumentId();

            $feedContents = ($available) ? $this->xml_availability($feedLists, $merchant_id, $currency_code) : $this->xml_availability($feedLists, $merchant_id, $currency_code);


            dd($feedContents);

            exit;
            $docToUpload = new Document($feedDocumentInfo, $feedType);
            $docToUpload->upload($feedContents);

            $FEED = $this->createFeed($apiInstance, $marketplace_ids, $feedDocumentId, $available);

            Log::info($FEED);

            return $FEED;

            //return json_decode(json_encode($result), true);
        } catch (Exception $e) {
            echo 'Exception when calling FeedsApi->createFeedDocument: ', $e->getMessage(), PHP_EOL;
        }
    }


    public function xml($feedLists, $merchant_id, $currency_code)
    {

        $messages = '';
        $counter = 1;

        foreach ($feedLists as $feedlist) {

            $messages .= '
                <Message>
                    <MessageID>' . $counter . '</MessageID>
                    <Price>
                        <SKU>' . $feedlist['sku'] . '</SKU>
                        <StandardPrice currency="' . $currency_code . '">' . $feedlist['new_my_price'] . '</StandardPrice>
                        <MinimumSellerAllowedPrice currency="' . $currency_code . '">' . $feedlist['minimum_seller_price'] . '</MinimumSellerAllowedPrice>
                    </Price>
                </Message>';

            $counter++;
        }

        $feed = '<?xml version="1.0" encoding="utf-8"?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
                <Header>
                    <DocumentVersion>1.01</DocumentVersion>
                    <MerchantIdentifier>' . $merchant_id . '</MerchantIdentifier>
                </Header>
                <MessageType>Price</MessageType>
                ' . $messages . '
            </AmazonEnvelope>
        ';

        return $feed;
    }

    public function xml_availability($feedLists, $merchant_id, $currency_code)
    {

        $messages = '';
        $counter = 1;

        foreach ($feedLists as $feedlist) {

            $messages .= '
                <Message>
                    <MessageID>' . $counter . '</MessageID>
                    <Inventory>
                        <SKU>' . $feedlist['sku'] . '</SKU>
                        <Available >' . $feedLists['available'] . ' </Available>
                        <FulfillmentLatency >' . $feedLists['latency'] . '</FulfillmentLatency>
                        <Quantity>25</Quantity>
                    </Inventory>
                </Message>';

            $counter++;
        }

        $feed = '<?xml version="1.0" encoding="utf-8"?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
                <Header>
                    <DocumentVersion>1.01</DocumentVersion>
                    <MerchantIdentifier>' . $merchant_id . '</MerchantIdentifier>
                </Header>
                <MessageType>Inventory</MessageType>
                ' . $messages . '
            </AmazonEnvelope>
        ';

        return $feed;
    }

    public function createFeed($apiInstance, $marketplace_ids, $feedDocumentId, $available = false)
    {

        $feedType = ($available) ? 'POST_INVENTORY_AVAILABILITY_DATA' : 'POST_PRODUCT_PRICING_DATA';

        //$apiInstance = new FeedsApi($this->config($aws_key, $country_code));
        $body = new CreateFeedSpecification(); // \SellingPartnerApi\Model\Feeds\CreateFeedSpecification
        $body->setFeedType($feedType);
        $body->setMarketplaceIds($marketplace_ids);
        $body->setInputFeedDocumentId($feedDocumentId);

        try {
            $result = $apiInstance->createFeed($body);

            Log::notice($result);
            return json_decode(json_encode($result), true);
        } catch (Exception $e) {
            echo 'Exception when calling FeedsApi->createFeed: ', $e->getMessage(), PHP_EOL;
        }
    }
}