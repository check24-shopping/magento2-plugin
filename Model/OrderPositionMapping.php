<?php


namespace Check24Shopping\OrderImport\Model;


use Check24Shopping\OrderImport\Api\Data\OrderPositionMappingInterface;
use Check24Shopping\OrderImport\Model\ResourceModel\OrderPositionMapping as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class OrderPositionMapping extends AbstractModel implements OrderPositionMappingInterface
{
    /** @var string */
    const CACHE_TAG = self::MODEL_NAME;

    /** @var string */
    protected $_cacheTag = self::MODEL_NAME;

    /** @var string */
    protected $_eventPrefix = self::MODEL_NAME;

    public function setId($id): OrderPositionMappingInterface
    {
        $this->setData(self::FIELD_ID, $id);

        return $this;
    }

    public function getId()
    {
        return $this->getData(self::FIELD_ID);
    }

    public function setCheck24OrderId(string $check24OrderId): OrderPositionMappingInterface
    {
        $this->setData(self::FIELD_CHECK24_ORDER_ID, $check24OrderId);

        return $this;
    }

    public function getCheck24OrderId()
    {
        return $this->getData(self::FIELD_CHECK24_ORDER_ID);
    }

    public function setCheck24PositionId(string $positionId): OrderPositionMappingInterface
    {
        $this->setData(self::FIELD_CHECK24_POSITION_ID, $positionId);

        return $this;
    }

    public function getCheck24PositionId()
    {
        return $this->getData(self::FIELD_CHECK24_POSITION_ID);
    }

    public function setMagentoPositionId(?int $positionId): OrderPositionMappingInterface
    {
        $this->setData(self::FIELD_MAGENTO_POSITION_ID, $positionId);

        return $this;
    }

    public function getMagentoPositionId(): int
    {
        return $this->getData(self::FIELD_MAGENTO_POSITION_ID);
    }

    public function setOrderUnit(string $orderUnit): OrderPositionMappingInterface
    {
        $this->setData(self::FIELD_ORDER_UNIT, $orderUnit);

        return $this;
    }

    public function getOrderUnit(): string
    {
        return $this->getData(self::FIELD_ORDER_UNIT);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
