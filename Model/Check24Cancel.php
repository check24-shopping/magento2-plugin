<?php

namespace Check24Shopping\OrderImport\Model;

use Check24Shopping\OrderImport\Api\Data\Check24CancelInterface;
use Check24Shopping\OrderImport\Model\ResourceModel\Check24Cancel as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class Check24Cancel extends AbstractModel implements Check24CancelInterface
{
    private const MODEL_NAME = 'check24shopping_orderimport_cancel';

    /** @var string */
    const CACHE_TAG = self::MODEL_NAME;

    /** @var string */
    protected $_cacheTag = self::MODEL_NAME;

    /** @var string */
    protected $_eventPrefix = self::MODEL_NAME;

    public function setId($id): Check24CancelInterface
    {
        $this->setData(self::FIELD_ID, $id);

        return $this;
    }

    public function getId()
    {
        return $this->getData(self::FIELD_ID);
    }

    public function setStatus($status): Check24CancelInterface
    {
        $this->setData(self::FIELD_STATUS, $status);

        return $this;
    }

    public function getStatus()
    {
        return $this->getData(self::FIELD_STATUS);
    }

    public function setErrorMessage($errorMessage): Check24CancelInterface
    {
        $this->setData(self::FIELD_ERROR_MESSAGE, $errorMessage);

        return $this;
    }

    public function getErrorMessage()
    {
        return $this->getData(self::FIELD_ERROR_MESSAGE);
    }

    public function setCreatedAt(string $createdAt): Check24CancelInterface
    {
        $this->setData(self::FIELD_CREATED_AT, $createdAt);

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->getData(self::FIELD_CREATED_AT);
    }

    public function setOrderId(string $orderId): Check24CancelInterface
    {
        $this->setData(self::FIELD_ORDER_ID, $orderId);

        return $this;
    }

    public function getOrderId()
    {
        return $this->getData(self::FIELD_ORDER_ID);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
