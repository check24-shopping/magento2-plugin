<?php

namespace Check24Shopping\OrderImport\Model;

use Check24Shopping\OrderImport\Api\Data\OrderImportInterface;
use Check24Shopping\OrderImport\Model\ResourceModel\OrderImport as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class OrderImport extends AbstractModel implements OrderImportInterface
{
    private const MODEL_NAME = 'check24_orderimport_orderimport';

    /** @var string */
    const CACHE_TAG = self::MODEL_NAME;

    /** @var string */
    protected $_cacheTag = self::MODEL_NAME;

    /** @var string */
    protected $_eventPrefix = self::MODEL_NAME;

    public function setId($id): OrderImportInterface
    {
        $this->setData(self::FIELD_ID, $id);

        return $this;
    }

    public function getId()
    {
        return $this->getData(self::FIELD_ID);
    }

    public function setCheck24OrderId(string $check24Id): OrderImportInterface
    {
        $this->setData(self::FIELD_CHECK24_ORDER_ID, $check24Id);

        return $this;
    }

    public function getCheck24OrderId()
    {
        return $this->getData(self::FIELD_CHECK24_ORDER_ID);
    }

    public function setContent(string $content): OrderImportInterface
    {
        $this->setData(self::FIELD_CONTENT, $content);

        return $this;
    }

    public function getContent()
    {
        return $this->getData(self::FIELD_CONTENT);
    }

    public function setStatus($status): OrderImportInterface
    {
        $this->setData(self::FIELD_STATUS, $status);

        return $this;
    }

    public function getStatus()
    {
        return $this->getData(self::FIELD_STATUS);
    }

    public function setMagentoOrderId(?int $magentoOrderId): OrderImportInterface
    {
        $this->setData(self::FIELD_MAGENTO_ORDER_ID, $magentoOrderId);

        return $this;
    }

    public function getMagentoOrderId(): int
    {
        return $this->getData(self::FIELD_MAGENTO_ORDER_ID);
    }

    public function setMagentoOrderIncrementId(?string $magentoOrderIncrementId): OrderImportInterface
    {
        $this->setData(self::FIELD_MAGENTO_ORDER_INCREMENT_ID, $magentoOrderIncrementId);

        return $this;
    }

    public function getMagentoOrderIncrementId()
    {
        return $this->getData(self::FIELD_MAGENTO_ORDER_INCREMENT_ID);
    }

    public function setErrorMessage($errorMessage): OrderImportInterface
    {
        $this->setData(self::FIELD_ERROR_MESSAGE, $errorMessage);

        return $this;
    }

    public function getErrorMessage()
    {
        return $this->getData(self::FIELD_ERROR_MESSAGE);
    }

    public function setType(string $type): OrderImportInterface
    {
        $this->setData(self::FIELD_TYPE, $type);

        return $this;
    }

    public function getType()
    {
        return $this->getData(self::FIELD_TYPE);
    }

    public function setAction(string $action): OrderImportInterface
    {
        $this->setData(self::FIELD_ACTION, $action);

        return $this;
    }

    public function getAction()
    {
        return $this->getData(self::FIELD_ACTION);
    }

    public function setCreatedAt(string $createdAt): OrderImportInterface
    {
        $this->setData(self::FIELD_CREATED_AT, $createdAt);

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->getData(self::FIELD_CREATED_AT);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
