<?php

namespace Check24\OrderImport\Service\OpenTrans;

use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderInterface;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderItemInterface;
use SimpleXMLElement;

class OpenTransWriterService
{
    public static function createRootElement(string $type): SimpleXMLElement
    {
        $root = new SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?><' . $type . '></' . $type . '>'
        );
        $root
            ->addAttribute('xmlns', 'http://www.opentrans.org/XMLSchema/2.1');
        $root
            ->addAttribute('xmlns:xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root
            ->addAttribute('xmlns:xmlns:bmecat', 'https://www.bme.de/initiativen/bmecat/bmecat-2005');
        $root
            ->addAttribute('xmlns:xmlns:xmime', 'http://www.w3.org/2005/05/xmlmime');
        $root
            ->addAttribute('version', '2.1');
        $root
            ->addAttribute(
                'xsi:xsi:schemaLocation',
                'http://www.opentrans.org/XMLSchema/2.1 ' .
                'https://merchantcenter.check24.de/sdk/opentrans/schema-definitions/xmldsig-core-schema.xsd'
            );

        return $root;
    }

    public static function addParties(SimpleXMLElement $element, OpenTransDataOrderInterface $openTransDocument)
    {
        $parties = $element->addChild('PARTIES');
        foreach ($openTransDocument->getParties() as $party) {
            $partyElement = $parties->addChild('PARTY');
            $partyElement
                ->addChild('bmecat:bmecat:PARTY_ID', $party->getId())
                ->addAttribute('type', $party->getIdType());
            $partyElement
                ->addChild('PARTY_ROLE', $party->getRole());
        }
    }

    public static function addSupplierIdRefChild(SimpleXMLElement $element, string $id): void
    {
        $element
            ->addChild('bmecat:bmecat:SUPPLIER_IDREF', $id)
            ->addAttribute('type', 'check24');
    }

    public static function addDeliveryIdRefChild(SimpleXMLElement $element, string $id): void
    {
        $element
            ->addChild('DELIVERY_IDREF', $id)
            ->addAttribute('type', 'check24');
    }

    public static function addBuyerIdRefChild(SimpleXMLElement $element, string $id): void
    {
        $element
            ->addChild('bmecat:bmecat:BUYER_IDREF', $id)
            ->addAttribute('type', 'check24');
    }

    public static function addItem(
        SimpleXMLElement                $element,
        OpenTransDataOrderItemInterface $orderItem,
        string                          $itemName
    ): SimpleXMLElement
    {
        $orderResponseItem = $element->addChild($itemName);
        $orderResponseItem->addChild('LINE_ITEM_ID', $orderItem->getId());
        $orderResponseItem
            ->addChild('PRODUCT_ID')
            ->addChild('bmecat:bmecat:SUPPLIER_PID', $orderItem->getSku());
        $orderResponseItem->addChild('QUANTITY', $orderItem->getQuantity());
        $orderResponseItem->addChild('bmecat:bmecat:ORDER_UNIT', $orderItem->getUnit());

        return $orderResponseItem;
    }
}
