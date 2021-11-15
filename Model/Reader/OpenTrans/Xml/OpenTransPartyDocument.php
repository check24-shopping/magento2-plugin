<?php

namespace Check24\OrderImport\Model\Reader\OpenTrans\Xml;

use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataAddressInterface;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataPartyInterface;
use DOMElement;

final class OpenTransPartyDocument implements OpenTransDataPartyInterface
{
    use DomHandlerTrait;

    /** @var OpenTransAddressDocument */
    private $address;

    public function __construct(DOMElement $domElement)
    {
        $this->domElement = $domElement;
        foreach ($this->domElement->getElementsByTagName('ADDRESS') as $address) {
            $this->address = new OpenTransAddressDocument($address);
        }
    }

    public function getRole(): string
    {
        return $this->getFirstTagValue("PARTY_ROLE");
    }

    public function getId(): string
    {
        return $this->getFirstTagValue("PARTY_ID");
    }

    public function getAddress(): OpenTransDataAddressInterface
    {
        return $this->address;
    }

    public function getIdType(): string
    {
        $elements = $this->domElement->getElementsByTagName('PARTY_ID');
        /** @var DOMElement $element */
        $element = $elements->item(0);

        return $element->getAttribute('type');
    }
}
