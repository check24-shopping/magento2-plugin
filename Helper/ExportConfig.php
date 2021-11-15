<?php

namespace Check24Shopping\OrderImport\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Unserialize\Unserialize;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class ExportConfig extends AbstractHelper
{
    /* General settings */
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_ENABLED = 'check24shopping_orderimport/productexport/enabled';
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_PUB = 'check24shopping_orderimport/productexport/pub';
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_CRON = 'check24shopping_orderimport/productexport/schedule';

    /* Product export filters */
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_CATEGORIES = 'check24shopping_orderimport/productexport_filters/included_categories';
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_ATTRIBUTESETS = 'check24shopping_orderimport/productexport_filters/included_attribute_sets';
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_INSTOCK = 'check24shopping_orderimport/productexport_filters/instock';

    /* Product export attribute mapping */
    /** @var string config path */
    const CONFIG_PATH_PRODUCTID = 'check24shopping_orderimport/productexport_attributes/attribute_id';
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_DESCRIPTION = 'check24shopping_orderimport/productexport_attributes/attribute_description';
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_NAME = 'check24shopping_orderimport/productexport_attributes/attribute_name';
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_MANUFACTURER = 'check24shopping_orderimport/productexport_attributes/attribute_manufacturer';
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_MPNR = 'check24shopping_orderimport/productexport_attributes/attribute_mpnr';
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_EAN = 'check24shopping_orderimport/productexport_attributes/attribute_ean';
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_PZN = 'check24shopping_orderimport/productexport_attributes/attribute_pzn';
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_PRICE = 'check24shopping_orderimport/productexport_attributes/attribute_price';
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_PRICEPERUNIT = 'check24shopping_orderimport/productexport_attributes/attribute_priceperunit';
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_DELIVERYTIME = 'check24shopping_orderimport/productexport_attributes/attribute_deliverytime';
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_PRICESHIPPING = 'check24shopping_orderimport/productexport_attributes/attribute_priceshipping';
    /** @var string config path */
    const CONFIG_PATH_PRODUCTEXPORT_WEIGHT = 'check24shopping_orderimport/productexport_attributes/attribute_weight';
    /** @var string scope to use */
    protected $scopeLevel;
    /** @var  null|array */
    private $_usedAttributes;
    /** @var  null|array */
    private $_mappedAttributes;
    /** @var  null|array */
    private $_defaultAttributes;

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
     * @return mixed
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_PRODUCTEXPORT_ENABLED, $this->scopeLevel, $storeId);
    }

    /**
     * @param null|int|string $storeId
     *
     * @return mixed
     */
    public function isPub($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_PRODUCTEXPORT_PUB, $this->scopeLevel, $storeId);
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

    /**
     * @param null|int|string $storeId
     *
     * @return mixed
     */
    public function useStockFilter($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_INSTOCK, $this->scopeLevel, $storeId);
    }

    /**
     * @param null|int|string $storeId
     *
     * @return mixed
     */
    public function getProductIdentifier($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTID, $this->scopeLevel, $storeId);
    }

    public function resetConfigs()
    {
        $this->_usedAttributes = null;
        $this->_mappedAttributes = null;
        $this->_defaultAttributes = null;
    }

    /**
     * @param null|int|string $storeId
     *
     * @return array
     */
    public function getUsedAttributes($storeId = null)
    {
        if (!$this->_usedAttributes) {
            $this->_usedAttributes = array(
                'id',
                'sku',
                'parent_product_ids',
                'category_ids',
                'image',
                'special_price',
                'special_from_date',
                'special_to_date'
            );

            foreach ($this->getMappedAttributes($storeId) as $c24attr => $mageAttr) {
                $this->_usedAttributes[] = $mageAttr;
            }
        }

        return $this->_usedAttributes;
    }

    /**
     * @param null|int|string $storeId
     *
     * @return array|null
     */
    public function getMappedAttributes($storeId = null)
    {

        if (!$this->_mappedAttributes) {

            // Process serialized array configs (product attribute <> default value)
            $serialized = array(
                'manufacturer' => $this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_MANUFACTURER,
                    $this->scopeLevel, $storeId),
                'delivery_time' => $this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_DELIVERYTIME,
                    $this->scopeLevel, $storeId),
                'price_shipping' => $this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_PRICESHIPPING,
                    $this->scopeLevel, $storeId)
            );
            $this->processSerialized($serialized);

            $this->_mappedAttributes['description'] =
                $this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_DESCRIPTION, $this->scopeLevel, $storeId);
            $this->_mappedAttributes['name'] =
                $this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_NAME, $this->scopeLevel, $storeId);

            $this->_mappedAttributes['ean'] =
                $this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_EAN, $this->scopeLevel, $storeId);

            $this->_mappedAttributes['price'] =
                $this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_PRICE, $this->scopeLevel, $storeId);

            // Optional attribute configurations
            if ($this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_MPNR, $this->scopeLevel, $storeId)) {
                $this->_mappedAttributes['mpnr'] =
                    $this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_MPNR, $this->scopeLevel, $storeId);
            }

            if ($this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_PZN, $this->scopeLevel, $storeId)) {
                $this->_mappedAttributes['pzn'] =
                    $this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_PZN, $this->scopeLevel, $storeId);
            }

            if ($this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_PRICEPERUNIT, $this->scopeLevel,
                $storeId)) {
                $this->_mappedAttributes['price_perunit'] =
                    $this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_PRICEPERUNIT, $this->scopeLevel,
                        $storeId);
            }

            if ($this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_WEIGHT, $this->scopeLevel, $storeId)) {
                $this->_mappedAttributes['weight'] =
                    $this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_WEIGHT, $this->scopeLevel, $storeId);
            }
        }

        return $this->_mappedAttributes;
    }

    /**
     * @param array $serializedConfigsArray
     */
    private function processSerialized($serializedConfigsArray)
    {
        foreach ($serializedConfigsArray as $attribute => $serialized) {
            $unserializedConfigArray = $this->unserializeConfigValue($serialized);
            if ($unserializedConfigArray) {
                foreach ($unserializedConfigArray as $valueArray) {
                    if ($valueArray['defaultvalue']) {
                        $this->setDefaultAttribute($attribute, $valueArray['defaultvalue']);
                    } elseif ($valueArray['mapped_attribute']) {
                        $this->_mappedAttributes[$attribute] = $valueArray['mapped_attribute'];
                    }

                    break; //Break just in case more than 1 configuration row per attribute was added
                }
            }
        }
    }

    /**
     * Unserialize config value
     * temporarily solution to get unserialized config value
     * should be deprecated in 2.3.x
     *
     * @param string|null $serialized
     *
     * @return mixed
     */
    public function unserializeConfigValue($serialized)
    {
        if (empty($serialized)) {
            return false;
        }

        if ($this->isSerialized($serialized)) {
            $unserializer = ObjectManager::getInstance()->get(Unserialize::class);
        } else {
            $unserializer = ObjectManager::getInstance()->get(Json::class);
        }

        return $unserializer->unserialize($serialized);
    }

    /**
     * Check if value is a serialized string
     *
     * @param string $value
     *
     * @return boolean
     */
    private function isSerialized($value)
    {
        return (boolean)preg_match('/^((s|i|d|b|a|O|C):|N;)/', $value);
    }

    /**
     * @param string $attribute
     * @param mixed $default
     */
    private function setDefaultAttribute($attribute, $default)
    {
        $this->_defaultAttributes[$attribute] = $default;
    }

    /**
     * Used for mapping the csv row values to correct order.
     *
     * @return string[]
     */
    public function getCsvFields()
    {
        return array(
            'id' => '',
            'manufacturer' => '',
            'mpnr' => '',
            'name' => '',
            'description' => '',
            'ean' => '',
            'pzn' => '',
            'category_path' => '',
            'image_url' => '',
            'price' => '',
            'price_perunit' => '',
            'deeplink' => '',
            'delivery_time' => '',
            'price_shipping' => '',
            'stock' => '',
            'weight' => ''
        );
    }

    /**
     * @return string[]
     */
    public function getRequiredFields()
    {
        return array(
            'id',
            'name',
            'ean',
            'price',
            'deeplink',
            'delivery_time'
        );
    }

    /**
     * @param null|int|string $storeId
     *
     * @return array|false|string[]
     */
    public function getIncludedAttributeSetIds($storeId = null)
    {
        $attributeSetIds = explode(',',
            $this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_ATTRIBUTESETS, $this->scopeLevel, $storeId));

        if (in_array('all', $attributeSetIds)) {
            return array();
        }

        return $attributeSetIds;
    }

    /**
     * @return array|null
     */
    public function getDefaultAttributes()
    {
        return $this->_defaultAttributes;
    }

    /**
     * @param null|int|string $storeId
     *
     * @return mixed
     */
    public function getIncludedCategoryIds($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCTEXPORT_CATEGORIES, $this->scopeLevel, $storeId);
    }

    /**
     * @return bool
     */
    public function useProductPrice()
    {
        $attributes = $this->getMappedAttributes();

        return ($attributes['price'] == 'price');
    }
}
