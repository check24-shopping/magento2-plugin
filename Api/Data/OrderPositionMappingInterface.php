<?php

namespace Check24\OrderImport\Api\Data;

interface OrderPositionMappingInterface
{
    const MODEL_NAME = 'check24_orderimport_positionmapping';
    const TABLE_NAME = 'check24_position_mapping';
    const FIELD_ID = 'id';
    const FIELD_CHECK24_ORDER_ID = 'check24_order_id';
    const FIELD_CHECK24_POSITION_ID = 'check24_position_id';
    const FIELD_MAGENTO_POSITION_ID = 'magento_position_id';
    const FIELD_ORDER_UNIT = 'order_unit';

    public function setId($id): self;

    public function getId();

    public function setCheck24OrderId(string $check24OrderId): self;

    public function getCheck24OrderId();

    public function setCheck24PositionId(string $positionId): self;

    public function getCheck24PositionId();

    public function setMagentoPositionId(int $positionId): self;

    public function getMagentoPositionId(): int;

    public function setOrderUnit(string $orderUnit): self;

    public function getOrderUnit(): string;
}
