<?php

namespace Check24\OrderImport\Model\ResourceModel\OrderImport;

use Check24\OrderImport\Model\OrderImport as Model;
use Check24\OrderImport\Model\ResourceModel\OrderImport as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_eventPrefix = 'check24_orders';
    protected $_eventObject = 'check24_orders';

    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
