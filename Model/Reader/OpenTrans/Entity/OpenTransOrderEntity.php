<?php

namespace Check24\OrderImport\Model\Reader\OpenTrans\Entity;

use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderInterface;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderItemCollection;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataPartyInterface;

class OpenTransOrderEntity implements OpenTransDataOrderInterface
{
    /** @var string */
    private $partnerId;
    /** @var string */
    private $orderId;
    /** @var float */
    private $shippingAmount;
    /** @var OpenTransDataOrderItemCollection */
    private $orderItems;
    /** @var OpenTransDataPartyInterface */
    private $invoiceParty;
    /** @var OpenTransDataPartyInterface */
    private $deliveryParty;
    /** @var OpenTransDataPartyInterface */
    private $supplierParty;
    /** @var string */
    private $documentId;

    public function __construct(
        string                           $documentId,
        string                           $partnerId,
        string                           $orderId,
        float                            $shippingAmount,
        OpenTransDataOrderItemCollection $orderItems,
        OpenTransDataPartyInterface      $invoiceParty,
        OpenTransDataPartyInterface      $deliveryParty,
        OpenTransDataPartyInterface      $supplierParty
    )
    {
        $this->partnerId = $partnerId;
        $this->orderId = $orderId;
        $this->shippingAmount = $shippingAmount;
        $this->orderItems = $orderItems;
        $this->invoiceParty = $invoiceParty;
        $this->deliveryParty = $deliveryParty;
        $this->supplierParty = $supplierParty;
        $this->documentId = $documentId;
    }

    public function getPartnerId(): string
    {
        return $this->partnerId;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getShippingAmount(): float
    {
        return $this->shippingAmount;
    }

    public function getOrderItems(): OpenTransDataOrderItemCollection
    {
        return $this->orderItems;
    }

    public function getInvoiceParty(): OpenTransDataPartyInterface
    {
        return $this->invoiceParty;
    }

    public function getDeliveryParty(): OpenTransDataPartyInterface
    {
        return $this->deliveryParty;
    }

    public function getSupplierParty(): OpenTransDataPartyInterface
    {
        return $this->supplierParty;
    }

    public function getParties(): array
    {
        return
            [
                $this->invoiceParty,
                $this->deliveryParty,
                $this->supplierParty,
            ];
    }

    public function getDocumentId(): int
    {
        return (int)$this->documentId;
    }

    public function getAction(): string
    {
        return '';
    }
}
