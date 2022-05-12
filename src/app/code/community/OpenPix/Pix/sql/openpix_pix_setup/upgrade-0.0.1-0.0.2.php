<?php

$setup = new Mage_Sales_Model_Resource_Setup("core_setup");

$setup->startSetup();

// openpix_correlationid - OpenPix Charge CorrelationID
if (
    !$this->getAttribute(
        Mage_Sales_Model_Order::ENTITY,
        "openpix_correlationid",
        "attribute_id"
    )
) {
    $setup->addAttribute(
        Mage_Sales_Model_Order::ENTITY,
        "openpix_correlationid",
        [
            "type" => "varchar",
            "input" => "text",
            "backend" => "",
            "frontend" => "",
            "label" => "OpenPix Charge CorrelationID",
            "class" => "",
            "global" => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            "visible" => false,
            "required" => false,
            "user_defined" => false,
            "default" => "",
            "searchable" => false,
            "filterable" => false,
            "comparable" => false,
            "visible_on_front" => false,
            "unique" => false,
        ]
    );
}

// openpix_paymentlinkurl - OpenPix Charge Payment Link URL
if (
    !$this->getAttribute(
        Mage_Sales_Model_Order::ENTITY,
        "openpix_paymentlinkurl",
        "attribute_id"
    )
) {
    $setup->addAttribute(
        Mage_Sales_Model_Order::ENTITY,
        "openpix_paymentlinkurl",
        [
            "type" => "varchar",
            "input" => "text",
            "backend" => "",
            "frontend" => "",
            "label" => "OpenPix Charge Payment Link URL",
            "class" => "",
            "global" => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            "visible" => false,
            "required" => false,
            "user_defined" => false,
            "default" => "",
            "searchable" => false,
            "filterable" => false,
            "comparable" => false,
            "visible_on_front" => false,
            "unique" => false,
        ]
    );
}

// openpix_qrcodeimage - OpenPix Charge Qr Code Image
if (
    !$this->getAttribute(
        Mage_Sales_Model_Order::ENTITY,
        "openpix_qrcodeimage",
        "attribute_id"
    )
) {
    $setup->addAttribute(
        Mage_Sales_Model_Order::ENTITY,
        "openpix_qrcodeimage",
        [
            "type" => "varchar",
            "input" => "text",
            "backend" => "",
            "frontend" => "",
            "label" => "OpenPix Charge Qr Code Image",
            "class" => "",
            "global" => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            "visible" => false,
            "required" => false,
            "user_defined" => false,
            "default" => "",
            "searchable" => false,
            "filterable" => false,
            "comparable" => false,
            "visible_on_front" => false,
            "unique" => false,
        ]
    );
}

// openpix_brcode - OpenPix Charge Br Code
if (
    !$this->getAttribute(
        Mage_Sales_Model_Order::ENTITY,
        "openpix_brcode",
        "attribute_id"
    )
) {
    $setup->addAttribute(Mage_Sales_Model_Order::ENTITY, "openpix_brcode", [
        "type" => "varchar",
        "input" => "text",
        "backend" => "",
        "frontend" => "",
        "label" => "OpenPix Charge Br Code",
        "class" => "",
        "global" => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        "visible" => false,
        "required" => false,
        "user_defined" => false,
        "default" => "",
        "searchable" => false,
        "filterable" => false,
        "comparable" => false,
        "visible_on_front" => false,
        "unique" => false,
    ]);
}

// openpix_endtoendid - OpenPix Charge Payment Link URL
if (
    !$this->getAttribute(
        Mage_Sales_Model_Order::ENTITY,
        "openpix_endtoendid",
        "attribute_id"
    )
) {
    $setup->addAttribute(Mage_Sales_Model_Order::ENTITY, "openpix_endtoendid", [
        "type" => "varchar",
        "input" => "text",
        "backend" => "",
        "frontend" => "",
        "label" => "OpenPix Charge EndToEndId",
        "class" => "",
        "global" => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        "visible" => false,
        "required" => false,
        "user_defined" => false,
        "default" => "",
        "searchable" => false,
        "filterable" => false,
        "comparable" => false,
        "visible_on_front" => false,
        "unique" => false,
    ]);
}

$setup->endSetup();
