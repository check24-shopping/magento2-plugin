<?php

namespace Check24Shopping\OrderImport\Model;

use Check24Shopping\OrderImport\Api\Data\ReturnRequestInterface;
use Check24Shopping\OrderImport\Model\ResourceModel\ReturnRequest as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class ReturnRequest extends AbstractModel implements ReturnRequestInterface
{
    /** @var string */
    const CACHE_TAG = self::MODEL_NAME;

    /** @var string */
    protected $_cacheTag = self::MODEL_NAME;

    /** @var string */
    protected $_eventPrefix = self::MODEL_NAME;

    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    public function setId($id): ReturnRequestInterface
    {
        $this->setData(self::FIELD_ID, $id);

        return $this;
    }

    public function getId()
    {
        return $this->getData(self::FIELD_ID);
    }

    public function setCheck24OrderId(string $check24Id): ReturnRequestInterface
    {
        $this->setData(self::FIELD_CHECK24_ORDER_ID, $check24Id);

        return $this;
    }

    public function getCheck24OrderId()
    {
        return $this->getData(self::FIELD_CHECK24_ORDER_ID);
    }

    public function setMagentoOrderId(?int $magentoOrderId): ReturnRequestInterface
    {
        $this->setData(self::FIELD_MAGENTO_ORDER_ID, $magentoOrderId);

        return $this;
    }

    public function getMagentoOrderId(): int
    {
        return $this->getData(self::FIELD_MAGENTO_ORDER_ID);
    }

    public function setMagentoOrderIncrementId(?string $magentoOrderIncrementId): ReturnRequestInterface
    {
        $this->setData(self::FIELD_MAGENTO_ORDER_INCREMENT_ID, $magentoOrderIncrementId);

        return $this;
    }

    public function getMagentoOrderIncrementId()
    {
        return $this->getData(self::FIELD_MAGENTO_ORDER_INCREMENT_ID);
    }

    public function setCreatedAt(string $createdAt): ReturnRequestInterface
    {
        $this->setData(self::FIELD_CREATED_AT, $createdAt);

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->getData(self::FIELD_CREATED_AT);
    }

    public function setOrderCreatedAt(string $createdAt): ReturnRequestInterface
    {
        $this->setData(self::FIELD_ORDER_CREATED_AT, $createdAt);

        return $this;
    }

    public function getOrderCreatedAt()
    {
        return $this->getData(self::FIELD_ORDER_CREATED_AT);
    }
}
