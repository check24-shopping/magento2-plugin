<?php

namespace Check24\OrderImport\Model\ResourceModel\Check24Cancel;

use Check24\OrderImport\Model\Check24Cancel as Model;
use Check24\OrderImport\Model\ResourceModel\Check24Cancel as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'check24_check24_cancel';
    protected $_eventObject = 'check24_shipment_collection';

    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
