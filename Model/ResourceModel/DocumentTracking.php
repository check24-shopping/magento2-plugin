<?php

namespace Check24Shopping\OrderImport\Model\ResourceModel;

use Check24Shopping\OrderImport\Api\Data\DocumentTrackingInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class DocumentTracking extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(DocumentTrackingInterface::TABLE_NAME, DocumentTrackingInterface::FIELD_ID);
    }
}
