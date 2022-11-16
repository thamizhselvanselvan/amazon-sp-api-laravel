<?php

namespace App\Services\Courier_Booking;

use Carbon\Carbon;
use App\Jobs\B2C\B2CBooking;
use App\Models\Catalog\PricingUs;
use App\Models\order\OrderUpdateDetail;
use App\Services\BB\PushAsin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;


class B2cshipBookingServices
{
    private $amazon_order_id;
    private $order_item_id;
    private $store_id;

    public function b2cdata($amazon_order_id, $order_item_id, $store_id)
    {
        $this->amazon_order_id = $amazon_order_id;
        $this->order_item_id = $order_item_id;
        $this->store_id = $store_id;
        $this->custom_percentage = 65;

        Log::alert($amazon_order_id);

        $order_details = DB::connection('order')
            ->select("SELECT
                oids.*,
                ord.amazon_order_identifier as order_id,
                ord.purchase_date as order_date,
                ord.payment_method_details as pay_method,
                oids.quantity_ordered as item,
                ord.buyer_info as mail
            FROM orders AS ord
            INNER join orderitemdetails AS oids
            ON ord.amazon_order_identifier = oids.amazon_order_identifier
            WHERE
            oids.amazon_order_identifier = '$amazon_order_id'
            AND
            oids.order_item_identifier = '$order_item_id'
        ");

        $consignee_data = [];

        foreach ($order_details as $key => $details) {
            $OrderID = $details->order_id;

            if (is_null($details->order_date)) {
                $purchase_date = 'NA';
            } else {
                $purchase_date = $details->order_date;
            }

            if (is_null($details->pay_method)) {
                $payment_method = 'NA';
            } else {
                $payment_method = $details->pay_method;
            }

            if (is_null($details->item)) {
                $pieces = '1';
            } else {
                $pieces = $details->item;
            }
            if (is_null($details->order_item_identifier)) {
                $invoice_no = 'NA';
            } else {
                $invoice_no = $details->order_item_identifier;
            }

            $buyer_data = $details->mail;

            $buyer_info = json_decode($details->mail);

            if ($buyer_data == '{}' || is_null($buyer_data)) {
                $email = 'NA';
            } else {
                $email = $buyer_info->BuyerEmail;
            }

            $asin = $details->asin;
            $item_name = $details->title;

            $consignee_details = json_decode($details->shipping_address);
            $consignee_tax = Json_decode($details->item_tax);
            if (is_null($consignee_tax)) {
                $consignee_tax_amt = 'NA';
            } else {
                $consignee_tax_amt = $consignee_tax->Amount;
            }

            if (is_null($consignee_tax)) {
                $consignee_tax_currency = 'NA';
            } else {
                $consignee_tax_currency = $consignee_tax->CurrencyCode;
            }

            $item_price = Json_decode($details->item_price);
            if (is_null($item_price)) {
                $item_price = 'NA';
            } else {
                $item_price = $item_price->Amount;
            }

            $consignee_name = $this->objKeyVerify($consignee_details, 'Name');

            $consignee_AddressLine1 = $this->objKeyVerify($consignee_details, 'AddressLine1');
            $consignee_AddressLine2 = $this->objKeyVerify($consignee_details, 'AddressLine2');
            $consignee_city = $this->objKeyVerify($consignee_details, 'City');
            $consignee_state = $this->objKeyVerify($consignee_details, 'StateOrRegion');
            $consignee_CountryCode = $this->objKeyVerify($consignee_details, 'CountryCode');
            $consignee_pincode = $this->objKeyVerify($consignee_details, 'PostalCode');
            $consignee_Phone = $this->objKeyVerify($consignee_details, 'Phone');
            $consignee_AddressType = $this->objKeyVerify($consignee_details, 'AddressType');

            $cat_data =   DB::connection('catalog')->select("SELECT dimensions FROM catalognewuss  where asin = '$asin'");

            $price = PricingUs::where('asin', $asin)->get('us_price');

            $height = '';
            $unit = '';
            $length = '';
            $weight = '';
            $width = '';
            $us_price = 'NA';

            if (isset($cat_data[0]->dimensions) && isset($price[0])) {

                $dimensions = $cat_data[0]->dimensions;
                $dmns_array = json_decode(($dimensions), true);
                $height = ($dmns_array[0]['package']['height']['value']);
                $unit = ($dmns_array[0]['package']['height']['unit']);
                $length = ($dmns_array[0]['package']['length']['value']);
                $weight = ($dmns_array[0]['package']['weight']['value']);
                $width = ($dmns_array[0]['package']['width']['value']);

                $us_price = $price[0]->us_price;
            } else {

                $getMessage = 'Item Details Not Avaliable';
                $operation  = 'B2CShip Booking';

                $slackMessage = "Message: $getMessage
                Asin: $asin,
                Order_id: $amazon_order_id,
                Operation: $operation";

                OrderUpdateDetail::where([
                    ['amazon_order_id', $this->amazon_order_id],
                    ['order_item_id', $this->order_item_id],
                ])->update(
                    [
                        'booking_status' => '0'
                    ]
                );

                $this->missingASINDetails($asin);

                Log::alert($slackMessage);
                // Log::channel('slack')->error($slackMessage);
                return false;
            }

            $data['OrderID'] =     $OrderID;
            $data['purchase_date'] =  $purchase_date;
            $data['payment method'] = $payment_method;
            $data['pieces'] = $pieces;
            $data['email'] = $email;
            $data['item_name'] = $item_name;
            $data['consignee_name'] = $consignee_name;
            $data['consignee_AddressLine1'] = $consignee_AddressLine1;
            $data['consignee_AddressLine2'] = $consignee_AddressLine2;
            $data['consignee_city'] = $consignee_city;
            $data['consignee_state'] = $consignee_state;
            $data['consignee_CountryCode'] = $consignee_CountryCode;
            $data['invoice_no'] = $invoice_no;
            $data['invoice_value'] = $item_price;
            $data['consignee_pincode'] = $consignee_pincode;
            $data['consignee_Phone'] = $consignee_Phone;
            $data['consignee_AddressType'] = $consignee_AddressType;
            $data['consignee_tax_amt'] =  $consignee_tax_amt;
            $data['consignee_tax_currency'] =  $consignee_tax_currency;
            $data['height'] = $height;
            $data['unit'] =   $unit;
            $data['length'] = $length;
            $data['weight'] =  $weight;
            $data['width'] = $width;
            $data['USA_price'] = $us_price;

            $consignee_data[] = $data;
        }

        $this->requestxml($consignee_data);
    }

    public function requestxml($consignee_values)
    {

        $consignor_xml = '';
        if (App::environment() == 'production') {
            if ($this->store_id == 6) {

                //Nitrouse
                $user_id = 'nitroushaulinc@gmail.com';
                $password = 'G79rC7@NIT';
                $client = 'NITROUS1';

                $consignor_xml = '<ConsignorName>NITROUS HAUL INC</ConsignorName>
                <ConsignorContactPerson>NITROUS HAUL INC</ConsignorContactPerson>
                <ConsignorAddressLine1>75 22, 37th Ave,</ConsignorAddressLine1>
                <ConsignorAddressLine2>Jackson Heights,</ConsignorAddressLine2>
                <ConsignorAddressLine3></ConsignorAddressLine3>
                <ConsignorCountry>USA</ConsignorCountry>
                <ConsignorState>NY</ConsignorState>
                <ConsignorCity>NY</ConsignorCity>
                <ConsignorPinCode>11372</ConsignorPinCode>
                <ConsignorMobileNo>16318127010</ConsignorMobileNo>
                <ConsignorEmailID>nitroushaulinc@gmail.com</ConsignorEmailID>
                <ConsignorTaxID></ConsignorTaxID>';
            } else if ($this->store_id == 5) {

                //MBM Cred
                $user_id = 'mm@newmedia.in';
                $password = 'G79rC7$$';
                $client = 'C1013';

                $consignor_xml = '<ConsignorName>Mailbox Mart INC</ConsignorName>
                <ConsignorContactPerson>Mailbox Mart INC</ConsignorContactPerson>
                <ConsignorAddressLine1>75 22, 37th Ave,</ConsignorAddressLine1>
                <ConsignorAddressLine2>Jackson Heights,</ConsignorAddressLine2>
                <ConsignorAddressLine3></ConsignorAddressLine3>
                <ConsignorCountry>US</ConsignorCountry>
                <ConsignorState>NY</ConsignorState>
                <ConsignorCity>NY</ConsignorCity>
                <ConsignorPinCode>11372</ConsignorPinCode>
                <ConsignorMobileNo>2019170336</ConsignorMobileNo>
                <ConsignorEmailID>mailboxmartinc@gmail.com</ConsignorEmailID>
                <ConsignorTaxID></ConsignorTaxID>';
            } else {

                Log::channel('slack')->error("B2C API Creds Issue");
            }
        } else {
            $user_id = 'humlofatro@vusra.com';
            $password = 'G79rC7';
            $client = 'C10000026';

            $consignor_xml = '<ConsignorName>Mosh Test</ConsignorName>
            <ConsignorContactPerson>Mosh Ecom pvt</ConsignorContactPerson>
            <ConsignorAddressLine1>210, UNIT NO.7B. BUSSA INDUSTRIAL ESTATE NEAR CENTURY BAZAAR,</ConsignorAddressLine1>
            <ConsignorAddressLine2>WORLI,</ConsignorAddressLine2>
            <ConsignorAddressLine3></ConsignorAddressLine3>
            <ConsignorCountry>IN</ConsignorCountry>
            <ConsignorState>MH</ConsignorState>
            <ConsignorCity>MUMBAI</ConsignorCity>
            <ConsignorPinCode>400025</ConsignorPinCode>
            <ConsignorMobileNo>2019170336</ConsignorMobileNo>
            <ConsignorEmailID>admin@moshecom.com</ConsignorEmailID>
            <ConsignorTaxID></ConsignorTaxID>';
        }

        foreach ($consignee_values as $data) {

            $orddate = Carbon::now()->format('d-M-Y');

            $xml = '<?xml version="1.0" encoding="UTF-8"?>
                    <ShipmentBookingRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="ShipmentBookingRequest.xsd">

                    <UserId>' . $user_id . '</UserId>
                    <Password>' . $password . '</Password>
                    <APIVersion>1.0</APIVersion>
                    <Client>' . $client . '</Client>
                    <AwbNo></AwbNo>
                    <RefNo>' . $data['OrderID'] . '</RefNo>
                    <BookingDate>' . $orddate . '</BookingDate>
                    
                    ' . $consignor_xml . '

                    <ConsigneeName>' . $this->cleanSpecialCharacters($data['consignee_name']) . '</ConsigneeName>
                    <ConsigneeContactPerson></ConsigneeContactPerson>
                    <ConsigneeAddressLine1> ' . (!empty($data['consignee_AddressLine1']) ? $this->cleanSpecialCharacters($data['consignee_AddressLine1']) : ',') . '</ConsigneeAddressLine1>
                    <ConsigneeAddressLine2>' . (!empty($data['consignee_AddressLine2']) ? $this->cleanSpecialCharacters($data['consignee_AddressLine2']) : ',') . '</ConsigneeAddressLine2>
                    <ConsigneeAddressLine3></ConsigneeAddressLine3>
                    <ConsigneeCountry>IN</ConsigneeCountry>
                    <ConsigneeState>karnataka</ConsigneeState>
                    <ConsigneeCity> ' . strtolower($data['consignee_city']) . ' </ConsigneeCity>
                    <ConsigneePinCode> ' . $data['consignee_pincode'] . '</ConsigneePinCode>
                    <ConsigneeMobile>' . (($data['consignee_Phone'] == "") ? '9897654565' : $this->cleanSpecialCharacters($data['consignee_Phone'])) . ' </ConsigneeMobile>
                    <ConsigneeEmailID> ' . $data['email'] . ' </ConsigneeEmailID>
                    <ConsigneeTaxID></ConsigneeTaxID>
                    <PacketType>SPX</PacketType>
                    <PaymentType>CREDIT</PaymentType>
                    <PacketDescription>' . $this->cleanSpecialCharacters($data['item_name']) . '.</PacketDescription>
                    <InvoiceNo>' .  $data['invoice_no'] . '</InvoiceNo>
                    <InvoiceValue>' . $this->calculateCustomValue($data['USA_price']) . '</InvoiceValue>
                    <CurrencyCode>USD</CurrencyCode>
                    <InvoiceValueINR>' . $data['invoice_value']  . '</InvoiceValueINR>
                    <CurrencyCodeINR>INR</CurrencyCodeINR>
                    <Pieces>' . $data['pieces'] . '</Pieces>
                    <ActualWeight>2</ActualWeight>

                    <PCSWeightDetails>
                        <PCSWeightDetail>
                             <Weight>' . (($data['weight'] > 0) ? $data['weight'] : 1) . '</Weight>
                             <Length>' . (($data['length'] > 0) ? $data['length'] : 1) . '</Length>
                             <Breadth>' . (($data['width'] > 0) ? $data['width'] : 1) . '</Breadth>
                             <Height>' . (($data['height'] > 0) ? $data['height'] : 1) . '</Height>
                            <PCS>1</PCS>
                        </PCSWeightDetail>
                    </PCSWeightDetails>

                    <PCSDescriptionDetails>
                        <PCSDescriptionDetail>
                            <Description>' . $this->cleanSpecialCharacters($data['item_name']) . '.</Description>
                            <HSNCode>1</HSNCode>
                            <Unit>' . $data['pieces'] . '</Unit>
                            <UnitValue>' . $this->calculateCustomValue($data['USA_price']) . '</UnitValue>
                        </PCSDescriptionDetail>
                    </PCSDescriptionDetails>
                </ShipmentBookingRequest>';
            $this->verifyApiResponse($this->getawb($xml));
        }
    }

    public function getawb($xmldata)
    {
        $url = "https://api.b2cship.us/B2CShipAPI.svc/ShipmentBooking";
        $headers = array(
            "Content-type: text/xml",
            "Content-length: " . strlen($xmldata),
            "Connection: close",
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 500);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmldata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $data = curl_exec($ch);

        //Log::info($data);

        return $data;
    }

    public function calculateCustomValue($invoice_amount)
    {
        return round(($invoice_amount * ($this->custom_percentage / 100)), 2);
    }

    public function cleanSpecialCharacters($string)
    {
        $string = str_replace('&', 'and', $string);
        return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
    }

    public function objKeyVerify($obj, $key)
    {
        if (isset($obj->$key)) {
            return $obj->$key;
        }
        return 'NA';
    }

    public function verifyApiResponse($api_response)
    {
        $data = json_decode(json_encode(simplexml_load_string($api_response)), true);

        if (array_key_exists('ErrorDetailCode', $data)) {

            $error = $data['ErrorDetailCode'];
            $error_desc = $data['ErrorDetailCodeDesc'];
            $order_id = $this->amazon_order_id;

            $slackMessage = "Message: $error_desc,
            Type: $error,
            Order_id: $order_id,
            Operation: 'B2Cship Booking Response'";
            // po($slackMessage);
            Log::channel('slack')->error($slackMessage);
        } else {

            $awb_no = $data['AWBNo'];
            OrderUpdateDetail::where([
                ['amazon_order_id', $this->amazon_order_id],
                ['order_item_id', $this->order_item_id],
            ])->update(
                [
                    'courier_awb' => $awb_no,
                    'booking_status' => '1'
                ]
            );
        }
    }

    public function missingASINDetails($asin)
    {
        $model_name = table_model_create(country_code: 'us', model: "Asin_source", table_name: "asin_source_");
        $model_name->upsert(
            [
                'asin' => $asin,
                'user_id' => '1',
                'status' => '0'
            ],
            ['user_asin_unique'],
            ['asin', 'user_id', 'status']
        );

        $model_name_des = table_model_create(country_code: 'us', model: "Asin_destination", table_name: "asin_destination_");
        $model_name_des->upsert(
            [
                'asin' => $asin,
                'user_id' => '1',
                'status' => '0',
                'priority' => '1'
            ],
            ['user_asin_unique'],
            ['asin', 'user_id', 'status', 'priority']
        );

        $product[] = [
            'seller_id' => '40',
            'active' => 1,
            'asin1' => $asin,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $product_lowest_price[] = [
            'asin' => $asin,
            'cyclic' => 0,
            'delist' => 0,
            'available' => 0,
            'priority'  => '1',
            'import_type' => 'Seller'
        ];

        (new PushAsin())->PushAsinToBBTable($product, $product_lowest_price, 'us', '1');
    }
}
