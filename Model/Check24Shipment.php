<?php

namespace Check24Shopping\OrderImport\Model;

use Check24Shopping\OrderImport\Api\Data\Check24ShipmentInterface;
use Check24Shopping\OrderImport\Model\ResourceModel\Check24Shipment as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class Check24Shipment extends AbstractModel implements Check24ShipmentInterface
{
    private const MODEL_NAME = 'check24_orderimport_shipment';

    /** @var string */
    const CACHE_TAG = self::MODEL_NAME;

    /** @var string */
    protected $_cacheTag = self::MODEL_NAME;

    /** @var string */
    protected $_eventPrefix = self::MODEL_NAME;

    public function setId($id): Check24ShipmentInterface
    {
        $this->setData(Check24ShipmentInterface::FIELD_ID, $id);

        return $this;
    }

    public function getId()
    {
        return $this->getData(Check24ShipmentInterface::FIELD_ID);
    }

    public function setStatus($status): Check24ShipmentInterface
    {
        $this->setData(self::FIELD_STATUS, $status);

        return $this;
    }

    public function getStatus()
    {
        return $this->getData(self::FIELD_STATUS);
    }

    public function setShipmentId(?int $shipmentId): Check24ShipmentInterface
    {
        $this->setData(self::FIELD_SHIPMENT_ID, $shipmentId);

        return $this;
    }

    public function getShipmentId(): int
    {
        return $this->getData(self::FIELD_SHIPMENT_ID);
    }

    public function setErrorMessage($errorMessage): Check24ShipmentInterface
    {
        $this->setData(self::FIELD_ERROR_MESSAGE, $errorMessage);

        return $this;
    }

    public function getErrorMessage()
    {
        return $this->getData(self::FIELD_ERROR_MESSAGE);
    }

    public function setCreatedAt(string $createdAt): Check24ShipmentInterface
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
