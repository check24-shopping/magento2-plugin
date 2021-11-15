<?php

namespace Check24Shopping\OrderImport\Helper\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class OrderConfig extends AbstractHelper
{
    const XML_PATH_SHIPPING_CARRIER = 'check24shopping_orderimport/orderImport/shipping_carrier';
    const XML_PATH_CRON_SCHEDULE = 'check24shopping_orderimport/orderImport/schedule';
    const XML_PATH_ENABLED = 'check24shopping_orderimport/orderImport/enabledImport';
    const XML_PATH_DEBUG = 'check24shopping_orderimport/orderImport/debug';
    const XML_PATH_IMPORT_ATTRIBUTE_ID = 'check24shopping_orderimport/orderImport/attribute_id';

    /** @var string scope to use */
    protected $scopeLevel;

    public function __construct(Context $context, StoreManagerInterface $storeManager)
    {
        parent::__construct($context);

        if ($storeManager->isSingleStoreMode()) {
            // use default scope, in case there are some runaway values in website or store level
            $this->scopeLevel = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        } else {
            $this->scopeLevel = ScopeInterface::SCOPE_STORE;
        }
    }

    /**
     * @param null|int|string $storeId
     *
     * @return bool
     */
    public function isDebugEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_DEBUG, $this->scopeLevel, $storeId);
    }

    /**
     * @param null|int|string $storeId
     *
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, $this->scopeLevel, $storeId);
    }

    /**
     * @param null|int|string $storeId
     *
     * @return mixed
     */
    public function getShippingCarrier($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SHIPPING_CARRIER, $this->scopeLevel, $storeId);
    }

    /**
     * @param null|int|string $storeId
     *
     * @return mixed
     */
    public function getCronSchedule($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CRON_SCHEDULE, $this->scopeLevel, $storeId);
    }

    public function getImportAttributeId($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_IMPORT_ATTRIBUTE_ID, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
