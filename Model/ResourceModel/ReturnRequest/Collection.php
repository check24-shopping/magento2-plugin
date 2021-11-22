<?php

namespace Check24Shopping\OrderImport\Model\ResourceModel\ReturnRequest;

use Check24Shopping\OrderImport\Api\Data\ReturnRequestInterface;
use Check24Shopping\OrderImport\Model\ReturnRequest;
use Check24Shopping\OrderImport\Model\ResourceModel\ReturnRequest as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = ReturnRequestInterface::FIELD_ID;
    protected $_eventPrefix = ReturnRequestInterface::MODEL_NAME;
    protected $_eventObject = ReturnRequestInterface::MODEL_NAME . '_collection';

    protected function _construct()
    {
        $this->_init(
            ReturnRequest::class,
            ResourceModel::class
        );
    }
}
