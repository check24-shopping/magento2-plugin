<?php

namespace Check24\OrderImport\Model\Reader\OpenTrans\Entity;

use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataDispatchOrderInterface;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderItemCollection;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataPartyInterface;

final class OpenTransDispatchOrderEntity extends OpenTransOrderEntity implements OpenTransDataDispatchOrderInterface
{
    /** @var string */
    private $trackNumber;
    /**
     * @var OpenTransDataPartyInterface
     */
    private $delivererParty;

    public function __construct(
        string                           $documentId,
        string                           $partnerId,
        string                           $orderId,
        float                            $shippingAmount,
        OpenTransDataOrderItemCollection $orderItems,
        OpenTransDataPartyInterface      $invoiceParty,
        OpenTransDataPartyInterface      $deliveryParty,
        OpenTransDataPartyInterface      $supplierParty,
        OpenTransDataPartyInterface      $delivererParty,
        string                           $trackNumber
    )
    {
        parent::__construct(
            $documentId,
            $partnerId,
            $orderId,
            $shippingAmount,
            $orderItems,
            $invoiceParty,
            $deliveryParty,
            $supplierParty
        );
        $this->trackNumber = $trackNumber;
        $this->delivererParty = $delivererParty;
    }

    public function getTrackNumber(): string
    {
        return $this->trackNumber;
    }

    public function getParties(): array
    {
        return array_merge(
            parent::getParties(),
            [$this->delivererParty]
        );
    }

    public function getDelivererParty(): OpenTransDataPartyInterface
    {
        return $this->delivererParty;
    }
}
