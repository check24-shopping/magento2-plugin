<?php

namespace Check24\OrderImport\Model\ResourceModel\OrderPositionMapping;

use Check24\OrderImport\Api\Data\OrderPositionMappingInterface;
use Check24\OrderImport\Model\OrderPositionMapping;
use Check24\OrderImport\Model\ResourceModel\OrderPositionMapping as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = OrderPositionMappingInterface::FIELD_ID;
    protected $_eventPrefix = OrderPositionMappingInterface::MODEL_NAME;
    protected $_eventObject = OrderPositionMappingInterface::MODEL_NAME . '_collection';

    protected function _construct()
    {
        $this->_init(
            OrderPositionMapping::class,
            ResourceModel::class
        );
    }
}
