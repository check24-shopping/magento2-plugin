<?php

namespace Check24Shopping\OrderImport\Model;

use Check24Shopping\OrderImport\Api\Data\DynamicConfigInterface;
use Check24Shopping\OrderImport\Api\Data\DynamicConfigInterfaceFactory;
use Check24Shopping\OrderImport\Api\DynamicConfigRepositoryInterface;
use Check24Shopping\OrderImport\Model\ResourceModel\DynamicConfig\CollectionFactory as DynamicConfigCollectionFactory;

class DynamicConfigRepository implements DynamicConfigRepositoryInterface
{
    /**
     * @var DynamicConfigInterfaceFactory
     */
    private $dynamicConfigFactory;
    /**
     * @var DynamicConfigCollectionFactory
     */
    private $dynamicConfigCollectionFactory;

    public function __construct(
        DynamicConfigInterfaceFactory  $dynamicConfigFactory,
        DynamicConfigCollectionFactory $dynamicConfigCollectionFactory
    )
    {
        $this->dynamicConfigFactory = $dynamicConfigFactory;
        $this->dynamicConfigCollectionFactory = $dynamicConfigCollectionFactory;
    }

    public function save(DynamicConfigInterface $dynamicConfig)
    {
        return $this->load()
            ->setData($dynamicConfig->getData())
            ->save();
    }

    public function load(): DynamicConfigInterface
    {
        $collection = $this->dynamicConfigCollectionFactory->create();
        $items = $collection->getItems();
        $config = reset($items);
        if ($config === false || empty($config->getId())) {
            $config = $this->dynamicConfigFactory->create();
            $config
                ->setProcessCancel(false)
                ->setProcessReturn(false)
                ->setSendCancel(false)
                ->setSendDispatch(false)
                ->setSendReturn(false)
                ->save();
        }

        return $config;
    }
}
