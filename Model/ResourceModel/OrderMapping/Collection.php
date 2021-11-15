<?php

namespace Check24\OrderImport\Model\ResourceModel\OrderMapping;

use Check24\OrderImport\Api\Data\OrderMappingInterface;
use Check24\OrderImport\Model\OrderMapping;
use Check24\OrderImport\Model\ResourceModel\OrderMapping as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = OrderMappingInterface::FIELD_ID;
    protected $_eventPrefix = OrderMappingInterface::MODEL_NAME;
    protected $_eventObject = OrderMappingInterface::MODEL_NAME . '_collection';

    protected function _construct()
    {
        $this->_init(
            OrderMapping::class,
            ResourceModel::class
        );
    }
}
