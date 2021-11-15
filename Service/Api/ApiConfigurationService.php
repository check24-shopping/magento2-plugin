<?php

namespace Check24\OrderImport\Service\Api;

use Check24\OrderImport\Helper\Config\ApiConfiguration;
use Check24\OrderImport\Helper\Config\OrderConfig;
use Check24\OrderImport\Helper\ConfigHelper;
use Check24\OrderImport\Model\Exception\NoValidConfiguration;
use Magento\Store\Model\StoreManagerInterface;

class ApiConfigurationService
{
    /** @var StoreManagerInterface */
    private $storeManager;
    /** @var ConfigHelper */
    private $baseConfig;
    /** @var array */
    private $apiConfigurations = [];
    /** @var OrderConfig */
    private $orderConfig;

    public function __construct(
        StoreManagerInterface $storeManager,
        OrderConfig           $orderConfig,
        ConfigHelper          $baseConfig
    )
    {
        $this->storeManager = $storeManager;
        $this->baseConfig = $baseConfig;
        $this->orderConfig = $orderConfig;
    }

    public function findAllApiConfigurations(): array
    {
        $configurations = [];
        foreach ($this->storeManager->getStores() as $store) {
            try {
                $apiConfiguration = $this->findStoreApiConfiguration($store->getId());
            } catch (NoValidConfiguration $exception) {
                continue;
            }

            if (empty($apiConfiguration) || key_exists($apiConfiguration->getId(), $configurations)) {
                continue;
            }

            $configurations[$apiConfiguration->getId()] = $apiConfiguration;
        }

        return $configurations;
    }

    /**
     * @throws NoValidConfiguration
     */
    public function findStoreApiConfiguration(int $storeId): ApiConfiguration
    {
        if (key_exists($storeId, $this->apiConfigurations) === false) {
            foreach ($this->storeManager->getStores() as $store) {
                if (empty($this->orderConfig->isEnabled($store->getId()))) {
                    continue;
                }

                $apiConfiguration = new ApiConfiguration(
                    $this->baseConfig->getPartnerId(),
                    $this->baseConfig->getUser($store->getId()),
                    $this->baseConfig->getPassword($store->getId()),
                    $this->baseConfig->getHost(),
                    $this->baseConfig->getPort()
                );

                if (key_exists($storeId, $this->apiConfigurations) === false) {
                    $this->apiConfigurations[$storeId] = $apiConfiguration;
                }
            }
        }
        if (key_exists($storeId, $this->apiConfigurations) === false) {
            throw new NoValidConfiguration();
        }

        return $this->apiConfigurations[$storeId];
    }
}
