<?php

namespace Check24Shopping\OrderImport\Api\Data;

interface OrderMappingInterface
{
    const MODEL_NAME = 'check24shopping_orderimport_ordermapping';
    const TABLE_NAME = 'check24_order_mapping';
    const FIELD_ID = 'id';
    const FIELD_PARTNER_ID = 'check24_partner_id';
    const FIELD_CHECK24_ORDER_ID = 'check24shopping_order_id';
    const FIELD_PARTY_DELIVERY_ID = 'party_delivery_id';
    const FIELD_PARTY_SUPPLIER_ID = 'party_supplier_id';
    const FIELD_PARTY_INVOICE_ISSUER_ID = 'party_invoice_issuer_id';
    const FIELD_MAGENTO_ORDER_ID = 'magento_order_id';
    const FIELD_MAGENTO_ORDER_INCREMENT_ID = 'magento_order_increment_id';
    const FIELD_CREATED_AT = 'created_at';

    public function setId($id): self;

    public function getId();

    public function setPartnerId(string $partnerId): self;

    public function getPartnerId();

    public function setCheck24OrderId(string $check24Id): self;

    public function getCheck24OrderId();

    public function setPartyDeliveryId(string $deliveryId): self;

    public function getPartyDeliveryId();

    public function setPartySupplierId(string $supplier): self;

    public function getPartySupplierId();

    public function setPartyInvoiceIssuerId(string $invoiceIssuerId): self;

    public function getPartyInvoiceIssuerId();

    public function setMagentoOrderId(?int $magentoOrderId): self;

    public function getMagentoOrderId(): int;

    public function setMagentoOrderIncrementId(?string $magentoOrderIncrementId): self;

    public function getMagentoOrderIncrementId();

    public function setCreatedAt(string $createdAt): self;

    public function getCreatedAt();
}
