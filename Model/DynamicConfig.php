<?php

namespace Check24\OrderImport\Model;

use Check24\OrderImport\Api\Data\DynamicConfigInterface;
use Check24\OrderImport\Model\ResourceModel\DynamicConfig as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class DynamicConfig extends AbstractModel implements DynamicConfigInterface
{
    /** @var string */
    const CACHE_TAG = self::TABLE_NAME;

    /** @var string */
    protected $_cacheTag = self::TABLE_NAME;

    /** @var string */
    protected $_eventPrefix = self::TABLE_NAME;

    public function setId($id): DynamicConfigInterface
    {
        $this->setData(self::FIELD_ID, $id);

        return $this;
    }

    public function getId()
    {
        return $this->getData(self::FIELD_ID);
    }

    public function setProcessCancel(bool $value): DynamicConfigInterface
    {
        $this->setData(self::FIELD_PROCESS_CANCEL, $value);

        return $this;
    }

    public function getProcessCancel(): bool
    {
        return (bool)$this->getData(self::FIELD_PROCESS_CANCEL);
    }

    public function setSendCancel(bool $value): DynamicConfigInterface
    {
        $this->setData(self::FIELD_SEND_CANCEL, $value);

        return $this;
    }

    public function getSendCancel(): bool
    {
        return (bool)$this->getData(self::FIELD_SEND_CANCEL);
    }

    public function setSendDispatch(bool $value): DynamicConfigInterface
    {
        $this->setData(self::FIELD_SEND_DISPATCH, $value);

        return $this;
    }

    public function getSendDispatch(): bool
    {
        return (bool)$this->getData(self::FIELD_SEND_DISPATCH);
    }

    public function setSendReturn(bool $value): DynamicConfigInterface
    {
        $this->setData(self::FIELD_SEND_RETURN, $value);

        return $this;
    }

    public function getSendReturn(): bool
    {
        return (bool)$this->getData(self::FIELD_SEND_RETURN);
    }

    public function setProcessReturn(bool $value): DynamicConfigInterface
    {
        $this->setData(self::FIELD_PROCESS_RETURN, $value);

        return $this;
    }

    public function getProcessReturn(): bool
    {
        return (bool)$this->getData(self::FIELD_PROCESS_RETURN);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
