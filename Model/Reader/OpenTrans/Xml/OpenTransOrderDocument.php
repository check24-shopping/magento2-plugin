<?php

namespace Check24\OrderImport\Model\Reader\OpenTrans\Xml;

use Check24\OrderImport\Model\Exception\DocumentIdMissing;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderInterface;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderItemCollection;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataPartyInterface;
use DOMDocument;
use DOMElement;
use Exception;

final class OpenTransOrderDocument implements OpenTransDataOrderInterface
{
    use DomHandlerTrait;

    /** @var OpenTransDataOrderItemCollection */
    private $orderItems;
    /** @var string */
    private $content;
    /** @var array|OpenTransDataPartyInterface[] */
    private $parties;

    public function __construct(string $content)
    {
        $this->domElement = new DOMDocument();
        $this->domElement->loadXML($content);
        $this->orderItems = new OpenTransDataOrderItemCollection();
        /** @var DOMElement $party */
        foreach ($this->domElement->getElementsByTagName('PARTY') as $party) {
            $this->parties[] = new OpenTransPartyDocument($party);
        }
        $this->content = $content;
    }

    public function getPartnerId(): string
    {
        return substr(strrchr($this->getFirstTagValue('SUPPLIER_IDREF'), '-'), 1);
    }

    public function getOrderId(): string
    {
        return (string)$this->getFirstTagValue('ORDER_ID');
    }

    public function getShippingAmount(): float
    {
        /** @var DOMElement $element */
        foreach ($this->domElement->getElementsByTagName('REMARKS') as $element) {
            if ($element->getAttribute('type') === 'shippingfee') {
                return $element->nodeValue;
            }
        }

        return 0.0;
    }

    public function getAction(): string
    {
        /** @var DOMElement $element */
        foreach ($this->domElement->getElementsByTagName('REMARKS') as $element) {
            if ($element->getAttribute('type') === 'action') {
                return strtolower((string)$element->nodeValue);
            }
        }

        return '';
    }

    public function getOrderItems(): OpenTransDataOrderItemCollection
    {
        if ($this->orderItems->isEmpty()) {
            /** @var DOMElement $item */
            foreach ($this->domElement->getElementsByTagName('ORDER_ITEM') as $item) {
                $this
                    ->orderItems
                    ->add(new OpenTransOrderItemDocument($item));
            }
        }

        return $this->orderItems;
    }

    public function getInvoiceParty(): OpenTransDataPartyInterface
    {
        return $this->getPartyType('invoice_issuer');
    }

    private function getPartyType(string $type): OpenTransDataPartyInterface
    {
        foreach ($this->parties as $party) {
            if ($party->getRole() === $type) {
                return $party;
            }
        }

        throw new Exception('party type "' . $type . '" is missing');
    }

    public function getDeliveryParty(): OpenTransDataPartyInterface
    {
        return $this->getPartyType('delivery');
    }

    public function getSupplierParty(): OpenTransDataPartyInterface
    {
        return $this->getPartyType('supplier');
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return array|OpenTransDataPartyInterface[]
     */
    public function getParties(): array
    {
        return $this->parties;
    }

    public function getDocumentId(): int
    {
        /** @var DOMElement $element */
        foreach ($this->domElement->getElementsByTagName('REMARKS') as $element) {
            if ($element->getAttribute('type') === 'documentnumber') {
                return (int)$element->nodeValue;
            }
        }

        throw new DocumentIdMissing('documentnumber is missing');
    }
}
