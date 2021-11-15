<?php

namespace Check24\OrderImport\Model\Writer\OpenTrans;

use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderInterface;
use Check24\OrderImport\Service\OpenTrans\OpenTransWriterService;
use SimpleXMLElement;

abstract class OpenTransResponse implements OpenTransGeneratorInterface
{
    /** @var OpenTransDataOrderInterface */
    private $openTransDocument;
    /** @var SimpleXMLElement */
    private $xmlDocument;

    public function __construct(OpenTransDataOrderInterface $openTransDocument)
    {
        $this->openTransDocument = $openTransDocument;
        $this->xmlDocument = $this->generateXmlDocument($openTransDocument);
    }

    private function generateXmlDocument(OpenTransDataOrderInterface $openTransDocument): SimpleXMLElement
    {
        $root = OpenTransWriterService::createRootElement($this->getType());
        $this->addHeader($root, $openTransDocument);
        $this->addItemList($root, $openTransDocument);
        $this->addSummary($root, $openTransDocument);

        return $root;
    }

    abstract protected function getType(): string;

    protected function addHeader(SimpleXMLElement $root, OpenTransDataOrderInterface $openTransDocument): SimpleXMLElement
    {
        $header = $root->addChild($this->getType() . '_HEADER');
        $info = $header->addChild($this->getType() . '_INFO');
        $info->addChild('ORDER_ID', $openTransDocument->getOrderId());
        $info->addChild($this->getType() . '_DATE', date('Y-m-d\TH:i:s'));
        $info->addChild('SUPPLIER_ORDER_ID', $openTransDocument->getOrderId());
        if ($this->getType() === 'ORDERCHANGE') {
            $info->addChild('ORDERCHANGE_SEQUENCE_ID', 1);
        }
        $this->addParties($info, $openTransDocument);
        $this->addPartiesReference($info, $openTransDocument);

        return $header;
    }

    private function addParties(SimpleXMLElement $element, OpenTransDataOrderInterface $openTransDocument)
    {
        $parties = $element->addChild('PARTIES');
        foreach ($openTransDocument->getParties() as $party) {
            $partyElement = $parties->addChild('PARTY');
            $partyElement
                ->addChild('bmecat:bmecat:PARTY_ID', $party->getId())
                ->addAttribute('type', 'check24');
            $partyElement
                ->addChild('PARTY_ROLE', $party->getRole());
        }
    }

    private function addPartiesReference(SimpleXMLElement $element, OpenTransDataOrderInterface $openTransDocument)
    {
        $reference = $element->addChild('ORDER_PARTIES_REFERENCE');
        $reference
            ->addChild('bmecat:bmecat:BUYER_IDREF', $openTransDocument->getDeliveryParty()->getId())
            ->addAttribute('type', 'check24');
        $reference
            ->addChild('bmecat:bmecat:SUPPLIER_IDREF', $openTransDocument->getSupplierParty()->getId())
            ->addAttribute('type', 'check24');
    }

    private function addItemList(SimpleXMLElement $element, OpenTransDataOrderInterface $openTransDocument)
    {
        $itemList = $element->addChild($this->getType() . '_ITEM_LIST');
        foreach ($openTransDocument->getOrderItems() as $orderItem) {
            OpenTransWriterService::addItem($itemList, $orderItem, $this->getItemName());
        }
    }

    abstract protected function getItemName(): string;

    private function addSummary(SimpleXMLElement $element, OpenTransDataOrderInterface $openTransDocument)
    {
        $element
            ->addChild($this->getType() . '_SUMMARY')
            ->addChild('TOTAL_ITEM_NUM', count($openTransDocument->getOrderItems()));
    }

    public function getXmlString(): string
    {
        return $this->xmlDocument->asXML();
    }
}
