<?php

namespace Check24\OrderImport\Model;

use Check24\OrderImport\Api\Data\Check24ReturnInterface;
use Check24\OrderImport\Model\ResourceModel\Check24Return as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class Check24Return extends AbstractModel implements Check24ReturnInterface
{
    private const MODEL_NAME = 'check24_orderimport_return';

    /** @var string */
    const CACHE_TAG = self::MODEL_NAME;

    /** @var string */
    protected $_cacheTag = self::MODEL_NAME;

    /** @var string */
    protected $_eventPrefix = self::MODEL_NAME;

    public function setId($id): Check24ReturnInterface
    {
        $this->setData(self::FIELD_ID, $id);

        return $this;
    }

    public function getId()
    {
        return $this->getData(self::FIELD_ID);
    }

    public function setStatus($status): Check24ReturnInterface
    {
        $this->setData(self::FIELD_STATUS, $status);

        return $this;
    }

    public function getStatus()
    {
        return $this->getData(self::FIELD_STATUS);
    }

    public function setErrorMessage($errorMessage): Check24ReturnInterface
    {
        $this->setData(self::FIELD_ERROR_MESSAGE, $errorMessage);

        return $this;
    }

    public function getErrorMessage()
    {
        return $this->getData(self::FIELD_ERROR_MESSAGE);
    }

    public function setCreatedAt(string $createdAt): Check24ReturnInterface
    {
        $this->setData(self::FIELD_CREATED_AT, $createdAt);

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->getData(self::FIELD_CREATED_AT);
    }

    public function setOrderId(string $orderId): Check24ReturnInterface
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
