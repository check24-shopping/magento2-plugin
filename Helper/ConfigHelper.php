<?php

namespace Check24\OrderImport\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class ConfigHelper extends AbstractHelper
{
    /** @var string config path */
    const CONFIG_PATH_PARTNER_ID = 'check24_orderimport/general/partnerId';
    /** @var string */
    const API_HOST = 'https://opentrans.shopping.check24.de';
    /** @var string config path */
    const CONFIG_PATH_FTP_USER = 'check24_orderimport/access/user';
    /** @var string config path */
    const CONFIG_PATH_FTP_PASSWORD = 'check24_orderimport/access/password';
    /** @var string */
    const API_PORT = '443';

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

    public function getHost(): string
    {
        return $_ENV['CHECK24_API_HOST'] ?? self::API_HOST;
    }

    /**
     * @param null|int|string $storeId
     *
     * @return mixed
     */
    public function getUser($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_FTP_USER, $this->scopeLevel, $storeId);
    }

    /**
     * @param null|int|string $storeId
     *
     * @return mixed
     */
    public function getPassword($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_FTP_PASSWORD, $this->scopeLevel, $storeId);
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $_ENV['CHECK24_API_PORT'] ?? self::API_PORT;
    }

    /**
     * @param null|int|string $storeId
     *
     * @return mixed
     */
    public function getPartnerId($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_PARTNER_ID, $this->scopeLevel, $storeId);
    }
}
