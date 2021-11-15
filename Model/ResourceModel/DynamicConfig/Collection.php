<?php

namespace Check24Shopping\OrderImport\Model\ResourceModel\DynamicConfig;

use Check24Shopping\OrderImport\Api\Data\DynamicConfigInterface;
use Check24Shopping\OrderImport\Model\DynamicConfig;
use Check24Shopping\OrderImport\Model\ResourceModel\DynamicConfig as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = DynamicConfigInterface::FIELD_ID;
    protected $_eventPrefix = DynamicConfigInterface::TABLE_NAME;
    protected $_eventObject = DynamicConfigInterface::TABLE_NAME . '_collection';

    protected function _construct()
    {
        $this->_init(
            DynamicConfig::class,
            ResourceModel::class
        );
    }
}
