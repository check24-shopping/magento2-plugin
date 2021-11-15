<?php

namespace Check24\OrderImport\Model\Reader\OpenTrans;

interface OpenTransDataOrderInterface
{
    public function getDocumentId(): int;

    public function getPartnerId(): string;

    public function getOrderId(): string;

    public function getShippingAmount(): float;

    public function getAction(): string;

    public function getOrderItems(): OpenTransDataOrderItemCollection;

    public function getInvoiceParty(): OpenTransDataPartyInterface;

    public function getDeliveryParty(): OpenTransDataPartyInterface;

    public function getSupplierParty(): OpenTransDataPartyInterface;

    /**
     * @return array|OpenTransDataPartyInterface[]
     */
    public function getParties(): array;
}
