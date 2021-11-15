<?php

namespace Check24\OrderImport\Model\Reader\OpenTrans\Xml;

use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderItemInterface;
use DOMElement;

final class OpenTransOrderItemDocument implements OpenTransDataOrderItemInterface
{
    use DomHandlerTrait;

    public function __construct(DOMElement $domElement)
    {
        $this->domElement = $domElement;
    }

    public function getSku(): string
    {
        return $this->getFirstTagValue('SUPPLIER_PID');
    }

    public function getQuantity(): int
    {
        return $this->getFirstTagValue('QUANTITY');
    }

    public function getPrice(): float
    {
        return $this->getFirstTagValue('PRICE_AMOUNT');
    }

    public function getDescriptionShort(): string
    {
        return $this->getFirstTagValue('DESCRIPTION_SHORT');
    }

    public function getId(): string
    {
        return $this->getFirstTagValue('LINE_ITEM_ID');
    }

    public function getUnit(): string
    {
        return $this->getFirstTagValue('ORDER_UNIT');
    }
}
