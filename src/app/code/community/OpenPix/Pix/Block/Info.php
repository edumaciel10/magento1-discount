<?php

class OpenPix_Pix_Block_Info extends Mage_Payment_Block_Info
{
    use OpenPix_Pix_Trait_ExceptionMessenger;
    use OpenPix_Pix_Trait_LogMessenger;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("openpix/pix/payment/info/openpix.phtml");
    }
}
