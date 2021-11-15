<?php

namespace Check24\OrderImport\Model\ResourceModel;

use Check24\OrderImport\Api\Data\Check24CancelInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Check24Cancel extends AbstractDb
{
    public function __construct(
        Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init(Check24CancelInterface::TABLE_NAME, Check24CancelInterface::FIELD_ID);
    }
}
