<?php

class OpenPix_Pix_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("openpix/pix/payment/form/openpix.phtml");
    }
}
