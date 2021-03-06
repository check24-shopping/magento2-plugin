<?php

namespace Check24Shopping\OrderImport\Model\ResourceModel\DocumentTracking;

use Check24Shopping\OrderImport\Api\Data\DocumentTrackingInterface;
use Check24Shopping\OrderImport\Model\DocumentTracking;
use Check24Shopping\OrderImport\Model\ResourceModel\DocumentTracking as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = DocumentTrackingInterface::FIELD_ID;
    protected $_eventPrefix = DocumentTrackingInterface::TABLE_NAME;
    protected $_eventObject = DocumentTrackingInterface::TABLE_NAME . '_collection';

    protected function _construct()
    {
        $this->_init(
            DocumentTracking::class,
            ResourceModel::class
        );
    }
}
