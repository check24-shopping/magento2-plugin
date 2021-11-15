<?php

namespace Check24\OrderImport\Model\Task;

use Check24\OrderImport\Helper\ExportConfig;
use Check24\OrderImport\Logger\Logger;
use Exception;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Repository as AttributeRepository;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

class ExportProductsTask
{
    const EXPORT_FILE = 'products_%s.csv';
    const EXPORT_BASEDIR = 'media';
    const EXPORT_FOLDER = 'check24';

    protected $_delimiter = ';';
    protected $_enclosure = '"';

    /** @var ProductCollectionFactory */
    private $productCollectionFactory;
    private $exportConfig;
    private $storeManager;
    private $stockHelper;
    private $directoryList;
    private $messageManager;
    private $attributeRepository;
    private $catalogHelper;
    private $categoryCollectionFactory;
    private $logger;
    private $imageHelper;
    private $missingFields = [];

    public function __construct(
        ProductCollectionFactory  $productCollectionFactory,
        ExportConfig              $exportConfig,
        StoreManagerInterface     $storeManager,
        Stock                     $stockHelper,
        DirectoryList             $directoryList,
        ManagerInterface          $messageManager,
        AttributeRepository       $attributeRepository,
        CatalogHelper             $catalogHelper,
        CategoryCollectionFactory $categoryCollectionFactory,
        Logger                    $logger,
        Image                     $imageHelper
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->exportConfig = $exportConfig;
        $this->storeManager = $storeManager;
        $this->stockHelper = $stockHelper;
        $this->directoryList = $directoryList;
        $this->messageManager = $messageManager;
        $this->attributeRepository = $attributeRepository;
        $this->catalogHelper = $catalogHelper;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->logger = $logger;
        $this->imageHelper = $imageHelper;
    }

    public function exportProducts($storeId = null)
    {
        if (!$storeId) {
            $stores = $this->getStoreConfigurations();

            foreach ($stores as $store => $file) {
                $this->getProductsCsv($store, $file);
                // Reset stored configurations to reload for each store view.
                $this->exportConfig->resetConfigs();
            }
        } else {
            $store = $this->storeManager->getStore($storeId);

            if ($this->exportConfig->isEnabled($store->getId())) {
                $this->getProductsCsv($store->getId(), sprintf(self::EXPORT_FILE, $store->getCode()));
            } else {
                $this->messageManager->addNoticeMessage('Store ' . $store->getName() . ': Product Export is not enabled.');
            }
        }
    }

    /**
     * Check order export store configuration.
     *
     * @return array
     */
    private function getStoreConfigurations()
    {
        $stores = [];

        foreach ($this->storeManager->getStores() as $store) {
            if (!$this->exportConfig->isEnabled($store->getId())) {
                continue;
            }

            $exportFile = sprintf(self::EXPORT_FILE, $store->getCode());

            if (!isset($stores[$exportFile])) {
                $stores[$store->getId()] = $exportFile;
            }
        }

        return $stores;
    }

    public function getProductsCsv($storeId, $file)
    {
        $startTime = microtime(true);

        // Use factory to create a new product collection
        $productCollection = $this->productCollectionFactory->create();

        // Only active
        $productCollection->addAttributeToFilter('status', ['eq' => '1']);
        // Only simple products
        $productCollection->addAttributeToFilter('type_id', ['eq' => 'simple']);

        // Apply filters
        if ($this->exportConfig->useStockFilter($storeId)) {
            $this->stockHelper->addInStockFilterToCollection($productCollection);
        }

        if ($includedCategories = $this->exportConfig->getIncludedCategoryIds($storeId)) {
            $productCollection->addCategoriesFilter(['in' => $includedCategories]);
        }

        $attributeSetIds = $this->exportConfig->getIncludedAttributeSetIds($storeId);
        if (sizeof($attributeSetIds)) {
            $productCollection->addFieldToFilter('attribute_set_id', ['in' => $attributeSetIds]);
        }

        // Get array of standard and mapped attributes combined.
        $attributes = $this->exportConfig->getUsedAttributes($storeId);

        foreach ($attributes as $attribute) {
            $productCollection->addAttributeToSelect($attribute);
        }
        $productCollection->addAttributeToSelect('description');
        $productCollection
            ->addStoreFilter($storeId)
            ->addUrlRewrite();

        $productCollection->joinField(
            'qty',
            'cataloginventory_stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );

        $productCollection->setPageSize(100);
        $pages = $productCollection->getLastPageNumber();
        $currentPage = 1;

        $path = $this->directoryList->getPath(self::EXPORT_BASEDIR) . DS . self::EXPORT_FOLDER;

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $fileName = $path . DS . $file;

        // Write CSV header
        $this->saveData($fileName, [array_keys($this->exportConfig->getCsvFields())]);

        $failed = 0;
        $productsCount = 0;
        do {
            $productCollection->setCurPage($currentPage);
            $productCollection->load();
            $csvData = [];
            foreach ($productCollection as $_product) {
                if ($row = $this->_getProductRow($_product, $storeId)) {
                    $csvData[] = $row;
                } else {
                    $failed++;
                }
            }

            if (count($csvData)) {
                //write products to CSV
                $this->saveData($fileName, $csvData, 'a');
            } else {
                $logMessage = 'Could not export products - please check the filter and attribute settings.';
                $this->messageManager->addNoticeMessage($logMessage);
                $this->logger->debug($logMessage);
            }
            $productsCount += count($csvData);
            $currentPage++;
            //clear collection and free memory
            $productCollection->clear();
        } while ($currentPage <= $pages);

        $stopTime = microtime(true);
        $logMessage = __(sprintf(
            'CHECK24 product export for Store %d completed in %f seconds; Products exported: %d; Failed products: %d.',
            $storeId,
            ($stopTime - $startTime),
            $productsCount,
            $failed
        ));

        $this->messageManager->addSuccessMessage($logMessage);
        $this->logger->debug($logMessage);
    }

    /**
     * Saving data row array into CSV
     *
     * @param string $file
     * @param array $data
     */
    private function saveData($file, $data, $mode = 'w')
    {
        $fh = fopen($file, $mode);
        foreach ($data as $dataRow) {
            $this->fputcsv($fh, $dataRow, $this->_delimiter, $this->_enclosure);
        }
        fclose($fh);
    }

    private function fputcsv(&$handle, $fields = [], $delimiter = ',', $enclosure = '"')
    {
        $str = '';
        $escape_char = '\\';
        foreach ($fields as $value) {
            if (strpos($value, $delimiter) !== false ||
                strpos($value, $enclosure) !== false ||
                strpos($value, "\n") !== false ||
                strpos($value, "\r") !== false ||
                strpos($value, "\t") !== false ||
                strpos($value, ' ') !== false) {
                $str2 = $enclosure;
                $escaped = 0;
                $len = strlen($value);
                for ($i = 0; $i < $len; $i++) {
                    if ($value[$i] == $escape_char) {
                        $escaped = 1;
                    } elseif (!$escaped && $value[$i] == $enclosure) {
                        $str2 .= $enclosure;
                    } else {
                        $escaped = 0;
                    }
                    $str2 .= $value[$i];
                }
                $str2 .= $enclosure;
                $str .= $str2 . $delimiter;
            } else {
                $str .= $enclosure . $value . $enclosure . $delimiter;
            }
        }
        $str = substr($str, 0, -1);
        $str .= "\n";

        return fwrite($handle, $str);
    }

    protected function _getProductRow(Product $product, string $storeId): ?array
    {
        $row = [];
        // ID / SKU
        $id = ($this->exportConfig->getProductIdentifier($storeId) == 'id') ?
            $product->getId() : $product->getSku();
        $row['id'] = $id;

        $mappedAttributes = $this->exportConfig->getMappedAttributes($storeId);

        foreach ($mappedAttributes as $c24field => $attributeCode) {
            $value = $this->_getAttributeValue($product, $attributeCode, $storeId);
            $row[$c24field] = $value;
        }

        $row['category_path'] = $this->_getProductCategoriesPath($product, $storeId);

        try {
            $row['image_url'] = $this->getProductImageUrl($product);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $row['image_url'] = '';
        }

        $row['deeplink'] = $product->getProductUrl();

        $row['stock'] = (int)$product->getQty();

        // Check for default values
        if ($defaultValues = $this->exportConfig->getDefaultAttributes()) {
            foreach ($defaultValues as $attr => $value) {
                $row[$attr] = $value;
            }
        }

        // Validate & sort
        foreach ($this->exportConfig->getRequiredFields() as $required) {
            if (!$row[$required]) {
                //Required value missing - log & skip row
                $errorMsg = 'Required value for ' . $required . ' missing for product ID ' . $product->getId() .
                    ' in Store ' . $storeId . ' - skipping product.';
                $this->logger->error($errorMsg);
                if (key_exists($required, $this->missingFields) === false) {
                    $this->messageManager->addSuccessMessage($errorMsg);
                    $this->missingFields[$required] = true;
                }

                return null;
            }
        }

        $csvFields = $this->exportConfig->getCsvFields();

        return array_replace($csvFields, $row);
    }

    /**
     * @param Product $product
     * @param $attributeCode
     * @return string|null
     */
    protected function _getAttributeValue($product, $attributeCode, $storeId)
    {
        if (!$product->getData($attributeCode) || is_object($product->getData($attributeCode)) || is_array($product->getData($attributeCode))) {
            return '';
        }

        $value = $this->_getRawAttributeValue($product, $attributeCode, $storeId);
        if ($value == 'no-display') {
            return '';
        }

        return $value;
    }

    /**
     * @param Product $product
     * @param $attributeCode
     * @param $storeId
     * @return mixed
     */
    protected function _getRawAttributeValue($product, $attributeCode, $storeId)
    {
        $attribute = $this->_getAttribute($attributeCode, $storeId);

        if (!$attribute) {
            return '';
        }

        switch ($attribute->getFrontendInput()) {
            case 'textarea':
                return str_replace(["\r\n", "\n", "\r"], '<br />', trim($product->getData($attributeCode)));
            case 'multiselect':
                $value = [];
                $valueIds = explode(',', $product->getData($attributeCode));
                foreach ($valueIds as $valueId) {
                    $value[] = $attribute->getSource()->getOptionText($valueId);
                }

                return implode(', ', $value);
            case 'select':
                return $product->getAttributeText($attributeCode);
            case 'price':
                return $this->_getProductPrice($product, $attributeCode);
            default:
                return trim($product->getData($attributeCode));
        }
    }

    /**
     * @param string $attributeCode
     * @return Magento\Catalog\Model\Product\Attribute\Repository
     */
    protected function _getAttribute($attributeCode, $storeId)
    {
        if (!isset($this->_attributes[$attributeCode])) {
            /** @var Magento\Eav\Model\Entity\Attribute $attribute */
            $attribute = $this->attributeRepository->get($attributeCode)->setStoreId($storeId);

            $this->_attributes[$attributeCode] = $attribute;
        }

        return $this->_attributes[$attributeCode];
    }

    /**
     * @return string
     */
    protected function _getProductPrice($product, $attributeCode)
    {
        if ($attributeCode == 'price' && $this->exportConfig->useProductPrice()) {
            // Check for special price
            if ($product->getSpecialPrice() &&
                (date("Y-m-d G:i:s") > $product->getSpecialFromDate() ||
                    !$product->getSpecialFromDate()) && (date("Y-m-d G:i:s") < $product->getSpecialToDate() ||
                    !$product->getSpecialToDate())) {
                $price = $this->catalogHelper->getTaxPrice($product, $product->getSpecialPrice(), true);
            } else {
                $price = $this->catalogHelper->getTaxPrice($product, $product->getPrice(), true);
            }
        } else {
            $price = $product->getData($attributeCode);
        }

        return number_format($price, '2');
    }

    /**
     * @return string
     */
    protected function _getProductCategoriesPath($product, $store)
    {
        $categoryIds = $product->getCategoryIds();

        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addAttributeToFilter('entity_id', ['in' => $categoryIds])
            ->addAttributeToSelect('name');

        $categoryPath = [];
        foreach ($categoryCollection as $category) {
            $categoryPath[] = $category->getName();
        }

        return implode(' > ', $categoryPath);
    }

    /**
     * @param Product $product
     *
     * @return string
     */
    public function getProductImageUrl($product)
    {
        try {
            $url =
                $this->imageHelper->init($product, 'product_image')->setImageFile($product->getData('image'))->getUrl();
        } catch (Exception $e) {
            $url = '';
            $attribute = $product->getResource()->getAttribute('image');
            if (!$product->getImage()) {
                $url = "";
            } elseif ($attribute) {
                $url = $attribute->getFrontend()->getUrl($product);
            }
        }
        if (!$this->exportConfig->isPub($product->getStoreId())) {
            $url = str_replace('pub/media', 'media', $url);
        }

        return $url;
    }
}
