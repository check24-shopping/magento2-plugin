<?php

namespace Check24Shopping\OrderImport\Model\ResourceModel\DynamicConfig;

use Magento\Framework\ObjectManagerInterface;

class CollectionFactory
{
    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var string */
    private $instanceName;

    public function __construct(
        ObjectManagerInterface $objectManager,
        string                 $instanceName = Collection::class
    )
    {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    public function create(array $data = []): Collection
    {
        return $this->objectManager->create($this->instanceName, $data);
    }
}
