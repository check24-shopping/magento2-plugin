<?php

namespace Check24\OrderImport\Model;

use Check24\OrderImport\Api\OrderManagementInterface;
use Check24\OrderImport\Helper\Config\OrderConfig;
use Check24\OrderImport\Helper\ConfigHelper;
use Magento\Store\Model\StoreManagerInterface;

class OrderManagement implements OrderManagementInterface
{
    /** @var StoreManagerInterface */
    private $storeManager;
    /** @var ConfigHelper */
    private $generalConfig;
    /** @var OrderConfig */
    private $orderConfig;

    public function __construct(
        StoreManagerInterface $storeManager,
        ConfigHelper          $generalConfig,
        OrderConfig           $orderConfig
    )
    {
        $this->storeManager = $storeManager;
        $this->generalConfig = $generalConfig;
        $this->orderConfig = $orderConfig;
    }

    public function getStoreIdByPartnerId($partnerId)
    {
        foreach ($this->storeManager->getStores() as $store) {
            if ($this->generalConfig->getPartnerId($store->getId()) == $partnerId) {
                return $store->getId();
            }
        }

        return null;
    }

    public function buildStreetData($street, $remarks, $storeId)
    {
        $streetData[] = $street;

        if ($remarks) {
            $streetData[] = $remarks;
        }

        return $streetData;
    }
}
