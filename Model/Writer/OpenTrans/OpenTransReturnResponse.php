<?php

namespace Check24\OrderImport\Model\Writer\OpenTrans;

use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderInterface;
use SimpleXMLElement;

final class OpenTransReturnResponse extends OpenTransResponse
{
    protected function getType(): string
    {
        return 'ORDERCHANGE';
    }

    protected function getItemName(): string
    {
        return 'ORDER_ITEM';
    }

    protected function addHeader(SimpleXMLElement $root, OpenTransDataOrderInterface $openTransDocument): SimpleXMLElement
    {
        $header = parent::addHeader($root, $openTransDocument);
        $children = $header->xpath('ORDERCHANGE_INFO');
        $info = reset($children);
        $info
            ->addChild('REMARKS', 'returnrequest')
            ->addAttribute('type', 'action');

        return $header;
    }
}
