<?php

namespace Check24\OrderImport\Model\ResourceModel\OrderImport\Grid;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init('Check24\OrderImport\Model\OrderImport', 'Check24\OrderImport\Model\ResourceModel\OrderImport');
    }
}
