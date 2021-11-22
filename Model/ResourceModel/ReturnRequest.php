<?php

namespace Check24Shopping\OrderImport\Model\ResourceModel;

use Check24Shopping\OrderImport\Api\Data\ReturnRequestInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class ReturnRequest extends AbstractDb
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
            ReturnRequestInterface::TABLE_NAME,
            ReturnRequestInterface::FIELD_ID
        );
    }
}
