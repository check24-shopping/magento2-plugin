<?php

namespace Check24Shopping\OrderImport\Model\ResourceModel\Check24Shipment;

use Check24Shopping\OrderImport\Model\Check24Shipment as Model;
use Check24Shopping\OrderImport\Model\ResourceModel\Check24Shipment as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'check24_check24_shipment';
    protected $_eventObject = 'check24_shipment_collection';

    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
