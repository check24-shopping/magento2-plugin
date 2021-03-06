<?php

namespace Check24Shopping\OrderImport\Model\ResourceModel;

use Check24Shopping\OrderImport\Api\Data\DynamicConfigInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class DynamicConfig extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(DynamicConfigInterface::TABLE_NAME, DynamicConfigInterface::FIELD_ID);
    }
}
