<?php

namespace Check24\OrderImport\Model\ResourceModel;

use Check24\OrderImport\Api\Data\DocumentTrackingInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class DocumentTracking extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(DocumentTrackingInterface::TABLE_NAME, DocumentTrackingInterface::FIELD_ID);
    }
}
