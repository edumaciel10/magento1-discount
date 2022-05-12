<?php

class OpenPix_Pix_Model_Observer
{
    use OpenPix_Pix_Trait_ExceptionMessenger;
    use OpenPix_Pix_Trait_LogMessenger;

    public function applyGiftback(Varien_Event_Observer $observer)
    {
        $this->log('OpenPix Observer Start', 'openpix_event.log');

        $order = $observer->getEvent()->getOrder();
        $quote = Mage::getSingleton("checkout/session")->getQuote();

        $customer = $this->orderHelper()->getCustomerData($quote, $order);

        if(!isset($customer['taxID'])) {
            return null;
        }

        if(strlen($customer['taxID']) !== 11) {
            return null;
        }

        $app_ID = $this->helper()->getAppID();

        if (!$app_ID) {
            $this->log("OpenPix Observer - AppID not found", 'openpix_event.log');
            $this->error("An error occurred while creating your order");
            return false;
        }

        $apiUrl = $this->helper()->getOpenPixApiUrl();

        this->log('openpix observer apiUrl ' . $apiUrl, 'openpix_event.log');

        $headers = [
            "Accept: application/json",
            "Content-Type: application/json; charset=utf-8",
            "Authorization: " . $app_ID,
            "platform: MAGENTO1",
            "version: 1.2.0",
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $apiUrl . "/api/openpix/v1/giftback/balance/" . $customer['taxID']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl) || $response === false) {
            $this->debugJson(
                "OpenPix Observer Curl Error - while creating Pix ", json_encode($response)
            );
            $this->error("An error occurred while creating your order");
            curl_close($curl);

            return false;
        }

        curl_close($curl);

        if ($statusCode === 401) {
            $this->log("OpenPix Observer Error 401 - Invalid AppID", 'openpix_event.log');
            $this->error("An error occurred while creating your order");

            return false;
        }

        if ($statusCode === 400) {
            $this->debugJson("OpenPix Observer Error 400 - ", json_encode($response), 'openpix_event.log');
            $this->error("An error occurred while creating your order");

            return false;
        }

        if ($statusCode !== 200) {
            $this->debugJson(
                "OpenPix Observer Error 400 - while creating Pix ", json_encode($response), 'openpix_event.log'
            );
            $this->error("An error occurred while creating your order");
            curl_close($curl);

            return false;
        }

        $response = json_decode($response, true);

        this->log('OpenPix Observer customer giftback balance response ' . $response, 'openpix_event.log');

        $giftbackBalance = $response['customer']['balance'];
        this->log('OpenPix Observer giftbackBalance ' . $giftbackBalance, 'openpix_event.log');

        $giftbackBalanceRounded = round($this->helper()->absint($giftbackBalance) / 100, 3);
        this->log('OpenPix Observer giftbackBalance ' . $giftbackBalance, 'openpix_event.log');

        // @todo calculate giftback balance on order total
        // @todo set the discount on order
        // @todo send the discount as giftbackAppliedValue to charge post api
    }

    protected function helper()
    {
        return Mage::helper("openpix_pix");
    }

    protected function orderHelper()
    {
        return Mage::helper("openpix_pix/order");
    }
}