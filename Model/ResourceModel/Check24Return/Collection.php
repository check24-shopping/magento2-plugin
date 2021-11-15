<?php

namespace Check24Shopping\OrderImport\Model\ResourceModel\Check24Return;

use Check24Shopping\OrderImport\Model\Check24Return as Model;
use Check24Shopping\OrderImport\Model\ResourceModel\Check24Return as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'check24_check24_Return';
    protected $_eventObject = 'check24_shipment_collection';

    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
