<?php

namespace Check24Shopping\OrderImport\Api\Data;

interface OrderImportInterface
{
    const TABLE_NAME = 'check24_document_import';
    const FIELD_ID = 'id';
    const FIELD_CHECK24_ORDER_ID = 'check24_order_id';
    const FIELD_CONTENT = 'content';
    const FIELD_MAGENTO_ORDER_ID = 'magento_order_id';
    const FIELD_MAGENTO_ORDER_INCREMENT_ID = 'magento_order_increment_id';
    const FIELD_ERROR_MESSAGE = 'error_message';
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_STATUS = 'status';
    const FIELD_TYPE = 'type';
    const FIELD_ACTION = 'action';

    public function setId($id): self;

    public function getId();

    public function setCheck24OrderId(string $check24Id): self;

    public function getCheck24OrderId();

    public function setAction(string $action): self;

    public function getAction();

    public function setContent(string $content): self;

    public function getContent();

    public function setStatus($status): self;

    public function getStatus();

    public function setMagentoOrderId(?int $magentoOrderId): self;

    public function getMagentoOrderId(): int;

    public function setMagentoOrderIncrementId(?string $magentoOrderIncrementId): self;

    public function getMagentoOrderIncrementId();

    public function setErrorMessage($errorMessage): self;

    public function getErrorMessage();

    public function setType(string $type): self;

    public function getType();

    public function setCreatedAt(string $createdAt): self;

    public function getCreatedAt();
}
