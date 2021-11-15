<?php

namespace Check24\OrderImport\Model\ResourceModel;

use Check24\OrderImport\Api\Data\DynamicConfigInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class DynamicConfig extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(DynamicConfigInterface::TABLE_NAME, DynamicConfigInterface::FIELD_ID);
    }
}
