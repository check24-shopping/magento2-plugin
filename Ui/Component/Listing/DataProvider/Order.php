<?php

namespace Check24Shopping\OrderImport\Ui\Component\Listing\DataProvider;

use Check24Shopping\OrderImport\Model\ResourceModel\OrderImport\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class Order extends AbstractDataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
}
