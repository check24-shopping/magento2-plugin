<?php

namespace Check24Shopping\OrderImport\Api\Data;

interface Check24ShipmentInterface
{
    const TABLE_NAME = 'check24_shipment';
    const FIELD_ID = 'id';
    const FIELD_SHIPMENT_ID = 'shipment_id';
    const FIELD_ERROR_MESSAGE = 'error_message';
    const FIELD_STATUS = 'status';
    const FIELD_CREATED_AT = 'created_at';

    public function setId($id): self;

    public function getId();

    public function setStatus($status): self;

    public function getStatus();

    public function setShipmentId(?int $shipmentId): self;

    public function getShipmentId(): int;

    public function setErrorMessage($errorMessage): self;

    public function getErrorMessage();

    public function setCreatedAt(string $createdAt): self;

    public function getCreatedAt();
}
