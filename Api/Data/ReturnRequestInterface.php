<?php

namespace Check24Shopping\OrderImport\Api\Data;

interface ReturnRequestInterface
{
    const MODEL_NAME = 'check24shopping_orderimport_returnrequest';
    const TABLE_NAME = 'check24_return_request';
    const FIELD_ID = 'id';
    const FIELD_CHECK24_ORDER_ID = 'check24shopping_order_id';
    const FIELD_MAGENTO_ORDER_ID = 'magento_order_id';
    const FIELD_MAGENTO_ORDER_INCREMENT_ID = 'magento_order_increment_id';
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_ORDER_CREATED_AT = 'order_created_at';

    public function setId($id): self;

    public function getId();

    public function setCheck24OrderId(string $check24Id): self;

    public function getCheck24OrderId();

    public function setMagentoOrderId(?int $magentoOrderId): self;

    public function getMagentoOrderId(): int;

    public function setMagentoOrderIncrementId(?string $magentoOrderIncrementId): self;

    public function getMagentoOrderIncrementId();

    public function setCreatedAt(string $createdAt): self;

    public function getCreatedAt();

    public function setOrderCreatedAt(string $createdAt): self;

    public function getOrderCreatedAt();
}
