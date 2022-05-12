<?php

class OpenPix_Pix_Helper_Data extends Mage_Core_Helper_Abstract
{
    const OPENPIX_ENV = 'development';
//            const OPENPIX_ENV = 'staging';
    // const OPENPIX_ENV = "production";

    public static function uuid_v4()
    {
        return sprintf(
            "%04x%04x-%04x-%04x-%04x-%04x%04x%04x",

            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    public function getOpenPixApiUrl()
    {
        if (self::OPENPIX_ENV === "development") {
            // use host.docker.internal when developing locally with docker
            // return "http://host.docker.internal:5001";
            return "http://host.docker.internal:5001";
        }

        if (self::OPENPIX_ENV === "staging") {
            return "https://api.openpix.dev";
        }

        // production
        return "https://api.openpix.com.br";
    }

    public function getAppID()
    {
        return Mage::getStoreConfig("payment/openpix_pix/app_ID");
    }

    public function getWebhookAuthorization()
    {
        return Mage::getStoreConfig(
            "payment/openpix_pix/webhook_authorization"
        );
    }

    public function getConfig($config)
    {
        return Mage::getStoreConfig("payment/openpix_pix/" . $config);
    }

    public function get_amount_openpix($total)
    {
        try {
            return $this->absint(
                $this->format_decimal((float) $total * 100, 2)
            ); // In cents.
        } catch (Exception $e) {
            //            throw new \Magento\Framework\Exception\LocalizedException(
            //                __($e->getMessage())
            //            );
        }

        return $this;
    }

    /**
     * Convert a value to non-negative integer.
     *
     * @param mixed $maybeint Data you wish to have converted to a non-negative integer.
     * @return int A non-negative integer.
     * @since 2.5.0
     *
     */
    public function absint($maybeint)
    {
        return abs((int) $maybeint);
    }

    /**
     * Format decimal numbers ready for DB storage.
     *
     * Sanitize, remove decimals, and optionally round + trim off zeros.
     *
     * This function does not remove thousands - this should be done before passing a value to the function.
     *
     * @param float|string $number Expects either a float or a string with a decimal separator only (no thousands).
     * @param mixed $dp number  Number of decimal points to use, blank to use woocommerce_price_num_decimals, or false to avoid all rounding.
     * @param bool $trim_zeros From end of string.
     * @return string
     */
    public function format_decimal($number, $dp = false, $trim_zeros = false)
    {
        $decimals = [",", ".", ""];

        // Remove locale from string.
        if (!is_float($number)) {
            $number = str_replace($decimals, ".", $number);

            // Convert multiple dots to just one.
            $number = preg_replace(
                '/\.(?![^.]+$)|[^0-9.-]/',
                "",
                wc_clean($number)
            );
        }

        if (false !== $dp) {
            $dp = intval("" === $dp ? 2 : $dp);
            $number = number_format(floatval($number), $dp, ".", "");
        } elseif (is_float($number)) {
            // DP is false - don't use number format, just return a string using whatever is given. Remove scientific notation using sprintf.
            $number = str_replace(
                $decimals,
                ".",
                sprintf("%." . 2 . "f", $number)
            );
            // We already had a float, so trailing zeros are not needed.
            $trim_zeros = true;
        }

        if ($trim_zeros && strstr($number, ".")) {
            $number = rtrim(rtrim($number, "0"), ".");
        }

        return $number;
    }

    public function getCustomerNameFromQuote($quote)
    {
        return implode(
            [
                $quote->getCustomerFirstname(),
                $quote->getCustomerMiddlename(),
                $quote->getCustomerLastname(),
            ],
            " "
        );
    }

    public function getModuleVersion($moduleName = null)
    {
        if ($moduleName === null) {
            /** @var string $moduleName */
            $moduleName = $this->_getModuleName();
        }

        if (!Mage::getConfig()->getNode(sprintf("modules/%s", $moduleName))) {
            return null;
        }

        return (string) Mage::getConfig()->getModuleConfig(
            $moduleName
        )->version;
    }

    // Validar numero de cpf
    public function validateCPF($cpf)
    {
        // Verificar se foi informado
        if (empty($cpf)) {
            return false;
        }

        // Remover caracteres especias
        $cpf = preg_replace("/[^0-9]/", "", $cpf);

        // Verifica se o numero de digitos informados
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se todos os digitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calcula os digitos verificadores para verificar se o
        // CPF é válido
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * ($t + 1 - $c);
            }

            $d = ((10 * $d) % 11) % 10;

            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    // Validar numero de CNPJ
    public function validateCNPJ($cnpj)
    {
        // Verificar se foi informado
        if (empty($cnpj)) {
            return false;
        }

        // Remover caracteres especias
        $cnpj = preg_replace("/[^0-9]/", "", $cnpj);

        // Verifica se o numero de digitos informados
        if (strlen($cnpj) != 14) {
            return false;
        }

        // Verifica se todos os digitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 0, $n = 0; $i < 12; $n += $cnpj[$i] * $b[++$i]);

        if ($cnpj[12] != (($n %= 11) < 2 ? 0 : 11 - $n)) {
            return false;
        }

        for ($i = 0, $n = 0; $i <= 12; $n += $cnpj[$i] * $b[$i++]);

        if ($cnpj[13] != (($n %= 11) < 2 ? 0 : 11 - $n)) {
            return false;
        }

        return true;
    }

    public function getOpenPixPluginUrlScript()
    {
        if (self::OPENPIX_ENV === "development") {
            return "http://localhost:4444/openpix.js";
        }

        if (self::OPENPIX_ENV === "staging") {
            return "https://plugin.openpix.com.br/v1/openpix-dev.js";
        }

        // production
        return "https://plugin.openpix.com.br/v1/openpix.js";
    }
}
