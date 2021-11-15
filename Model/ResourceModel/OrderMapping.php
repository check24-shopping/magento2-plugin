<?php

namespace Check24Shopping\OrderImport\Model\ResourceModel;

use Check24Shopping\OrderImport\Api\Data\OrderMappingInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class OrderMapping extends AbstractDb
{
    /** @var string */
    protected $_idFieldName = 'entity_id';

    public function __construct(
        Context $context,
        ?string $resourcePrefix = null
    )
    {
        parent::__construct($context, $resourcePrefix);
    }

    protected function _construct()
    {
        $this->_init(
            OrderMappingInterface::TABLE_NAME,
            OrderMappingInterface::FIELD_ID
        );
    }
}
