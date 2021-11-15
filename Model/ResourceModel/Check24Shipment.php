<?php

namespace Check24Shopping\OrderImport\Model\ResourceModel;

use Check24Shopping\OrderImport\Api\Data\Check24ShipmentInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Check24Shipment extends AbstractDb
{
    public function __construct(
        Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init(Check24ShipmentInterface::TABLE_NAME, Check24ShipmentInterface::FIELD_ID);
    }
}
