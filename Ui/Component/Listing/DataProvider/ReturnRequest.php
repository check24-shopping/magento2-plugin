<?php

namespace Check24Shopping\OrderImport\Ui\Component\Listing\DataProvider;

use Check24Shopping\OrderImport\Model\ResourceModel\ReturnRequest\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class ReturnRequest extends AbstractDataProvider
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
