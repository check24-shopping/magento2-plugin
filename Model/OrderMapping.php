<?php

namespace Check24\OrderImport\Model;

use Check24\OrderImport\Api\Data\OrderMappingInterface;
use Check24\OrderImport\Model\ResourceModel\OrderMapping as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class OrderMapping extends AbstractModel implements OrderMappingInterface
{
    /** @var string */
    const CACHE_TAG = self::MODEL_NAME;

    /** @var string */
    protected $_cacheTag = self::MODEL_NAME;

    /** @var string */
    protected $_eventPrefix = self::MODEL_NAME;

    public function setId($id): OrderMappingInterface
    {
        $this->setData(self::FIELD_ID, $id);

        return $this;
    }

    public function getId()
    {
        return $this->getData(self::FIELD_ID);
    }

    public function setCheck24OrderId(string $check24Id): OrderMappingInterface
    {
        $this->setData(self::FIELD_CHECK24_ORDER_ID, $check24Id);

        return $this;
    }

    public function getCheck24OrderId()
    {
        return $this->getData(self::FIELD_CHECK24_ORDER_ID);
    }

    public function setMagentoOrderId(?int $magentoOrderId): OrderMappingInterface
    {
        $this->setData(self::FIELD_MAGENTO_ORDER_ID, $magentoOrderId);

        return $this;
    }

    public function getMagentoOrderId(): int
    {
        return $this->getData(self::FIELD_MAGENTO_ORDER_ID);
    }

    public function setMagentoOrderIncrementId(?string $magentoOrderIncrementId): OrderMappingInterface
    {
        $this->setData(self::FIELD_MAGENTO_ORDER_INCREMENT_ID, $magentoOrderIncrementId);

        return $this;
    }

    public function getMagentoOrderIncrementId()
    {
        return $this->getData(self::FIELD_MAGENTO_ORDER_INCREMENT_ID);
    }

    public function setCreatedAt(string $createdAt): OrderMappingInterface
    {
        $this->setData(self::FIELD_CREATED_AT, $createdAt);

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->getData(self::FIELD_CREATED_AT);
    }

    public function setPartyDeliveryId(string $deliveryId): OrderMappingInterface
    {
        $this->setData(self::FIELD_PARTY_DELIVERY_ID, $deliveryId);

        return $this;
    }

    public function getPartyDeliveryId()
    {
        return $this->getData(self::FIELD_PARTY_DELIVERY_ID);
    }

    public function setPartySupplierId(string $supplier): OrderMappingInterface
    {
        $this->setData(self::FIELD_PARTY_SUPPLIER_ID, $supplier);

        return $this;
    }

    public function getPartySupplierId()
    {
        return $this->getData(self::FIELD_PARTY_SUPPLIER_ID);
    }

    public function setPartyInvoiceIssuerId(string $invoiceIssuerId): OrderMappingInterface
    {
        $this->setData(self::FIELD_PARTY_INVOICE_ISSUER_ID, $invoiceIssuerId);

        return $this;
    }

    public function getPartyInvoiceIssuerId()
    {
        return $this->getData(self::FIELD_PARTY_INVOICE_ISSUER_ID);
    }

    public function setPartnerId(string $partnerId): OrderMappingInterface
    {
        $this->setData(self::FIELD_PARTNER_ID, $partnerId);

        return $this;
    }

    public function getPartnerId()
    {
        return $this->getData(self::FIELD_PARTNER_ID);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
