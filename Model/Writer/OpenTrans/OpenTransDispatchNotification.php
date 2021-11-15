<?php

namespace Check24\OrderImport\Model\Writer\OpenTrans;

use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataDispatchOrderInterface;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderInterface;
use Check24\OrderImport\Service\OpenTrans\OpenTransWriterService;
use SimpleXMLElement;

final class OpenTransDispatchNotification implements OpenTransGeneratorInterface
{
    private const TYPE = 'DISPATCHNOTIFICATION';
    /** @var OpenTransDataOrderInterface */
    private $openTransDocument;
    /** @var SimpleXMLElement */
    private $xmlDocument;

    public function __construct(OpenTransDataDispatchOrderInterface $openTransDocument)
    {
        $this->openTransDocument = $openTransDocument;
        $this->xmlDocument = $this->generateXmlDocument($openTransDocument);
    }

    private function generateXmlDocument(OpenTransDataDispatchOrderInterface $openTransDocument): SimpleXMLElement
    {
        $root = OpenTransWriterService::createRootElement(self::TYPE);
        $this->addHeader($root, $openTransDocument);
        $this->addItemList($root, $openTransDocument);
        $this->addSummary($root, $openTransDocument);

        return $root;
    }

    private function addHeader(SimpleXMLElement $root, OpenTransDataDispatchOrderInterface $openTransDocument): void
    {
        $header = $root->addChild(self::TYPE . '_HEADER');
        $info = $header->addChild(self::TYPE . '_INFO');
        $info->addChild(self::TYPE . '_ID', $openTransDocument->getOrderId());
        $info->addChild(self::TYPE . '_DATE', date('Y-m-d\TH:i:s'));
        OpenTransWriterService::addParties($info, $openTransDocument);
        OpenTransWriterService::addSupplierIdRefChild($info, $openTransDocument->getSupplierParty()->getId());
        $this->addPartiesReference($info, $openTransDocument);
        $info->addChild('SHIPMENT_ID', $openTransDocument->getTrackNumber());
    }

    private function addPartiesReference(SimpleXMLElement $element, OpenTransDataOrderInterface $openTransDocument)
    {
        $reference = $element->addChild('SHIPMENT_PARTIES_REFERENCE');
        OpenTransWriterService::addDeliveryIdRefChild($reference, $openTransDocument->getDeliveryParty()->getId());
    }

    private function addItemList(SimpleXMLElement $element, OpenTransDataOrderInterface $openTransDocument)
    {
        $itemList = $element->addChild(self::TYPE . '_ITEM_LIST');
        foreach ($openTransDocument->getOrderItems() as $orderItem) {
            $orderResponseItem = OpenTransWriterService::addItem($itemList, $orderItem, self::TYPE . '_ITEM');

            $orderReference = $orderResponseItem->addChild('ORDER_REFERENCE');
            $orderReference->addChild('ORDER_ID', $openTransDocument->getOrderId());
            $orderReference->addChild('LINE_ITEM_ID', $orderItem->getId());

            $partiesReference = $orderResponseItem->addChild('SHIPMENT_PARTIES_REFERENCE');
            OpenTransWriterService::addDeliveryIdRefChild(
                $partiesReference,
                $openTransDocument->getDeliveryParty()->getId()
            );
        }
    }

    private function addSummary(SimpleXMLElement $element, OpenTransDataOrderInterface $openTransDocument)
    {
        $element
            ->addChild(self::TYPE . '_SUMMARY')
            ->addChild('TOTAL_ITEM_NUM', count($openTransDocument->getOrderItems()));
    }

    public function getXmlString(): string
    {
        return $this->xmlDocument->asXML();
    }
}
