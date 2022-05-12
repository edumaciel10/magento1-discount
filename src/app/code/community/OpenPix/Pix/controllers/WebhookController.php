<?php

class OpenPix_Pix_WebhookController extends Mage_Core_Controller_Front_Action
{
    use OpenPix_Pix_Trait_LogMessenger;
    use OpenPix_Pix_Trait_ExceptionMessenger;

    /**
     * Webhook Route
     */
    public function indexAction()
    {
        $this->logWebhook(sprintf("Start webhook"), Zend_Log::INFO);

        $handler = Mage::helper("openpix_pix/webhookHandler");

        if (!$this->validateRequest()) {
            $this->logWebhook(sprintf("Invalid Request"), Zend_Log::ERR);

            $authorization = $this->getRequest()->getHeader("Authorization");

            $this->logWebhook(
                sprintf("Invalid Authorization: " . $authorization),
                Zend_Log::ERR
            );

            $ip = Mage::helper("core/http")->getRemoteAddr();

            $this->logWebhook(
                sprintf("Invalid webhook attempt from IP %s", $ip),
                Zend_Log::WARN
            );

            header("HTTP/1.2 400 Bad Request");
            $response = [
                "error" => "Invalid Request",
                "description" =>
                    "Invalid authorization header: " . $authorization,
                "field" => "authorization header",
            ];
            echo json_encode($response);
            exit();
        }

        $body = file_get_contents("php://input");

        $result = $handler->handle($body);

        $this->logWebhook(
            sprintf("Webhook result " . json_encode($result)),
            Zend_Log::INFO
        );

        if (isset($result["error"])) {
            header("HTTP/1.2 400 Bad Request");
            $response = [
                "error" => "Invalid Webhook Authorization: " . $result["error"],
            ];
            echo json_encode($response);
            exit();
        }

        header("HTTP/1.1 200 OK");

        $response = [
            "success" => $result["success"],
        ];
        echo json_encode($response);
        exit();
    }

    /**
     * Validate webhook authorization
     *
     * @return bool
     */
    protected function validateRequest()
    {
        $webhookAuthSystem = Mage::helper(
            "openpix_pix"
        )->getWebhookAuthorization();

        $webhookAuthHeader = $this->getRequest()->getHeader("Authorization");
        $webhookAuthOpenPixHeader = $this->getRequest()->getHeader(
            "X-OpenPix-Authorization"
        );
        $webhookAuthQueryString = $this->getRequest()->getQuery(
            "authorization"
        );

        $isAuthHeaderValid = $webhookAuthSystem === $webhookAuthHeader;
        $isAuthOpenPixHeaderValid =
            $webhookAuthSystem === $webhookAuthOpenPixHeader;
        $isAuthQueryStringValid =
            $webhookAuthSystem === $webhookAuthQueryString;

        return $isAuthHeaderValid ||
            $isAuthOpenPixHeaderValid ||
            $isAuthQueryStringValid;
    }
}
