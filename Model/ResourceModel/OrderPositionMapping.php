<?php

namespace Check24\OrderImport\Model\ResourceModel;

use Check24\OrderImport\Api\Data\OrderPositionMappingInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class OrderPositionMapping extends AbstractDb
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
            OrderPositionMappingInterface::TABLE_NAME,
            OrderPositionMappingInterface::FIELD_ID
        );
    }
}
