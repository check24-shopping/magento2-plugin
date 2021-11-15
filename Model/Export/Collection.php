<?php

namespace Check24Shopping\OrderImport\Model\Export;

use Check24Shopping\OrderImport\Model\Task\ExportProductsTask;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;

class Collection extends DataCollection
{
    protected $_items;
    protected $directoryList;
    protected $context;

    public function __construct(
        StoreManagerInterface  $storeManager,
        EntityFactoryInterface $entityFactory,
        DirectoryList          $directoryList,
        ContextInterface       $context
    )
    {
        parent::__construct($entityFactory);
        $this->directoryList = $directoryList;
        $this->context = $context;

        foreach ($storeManager->getStores() as $store) {
            $item = [
                'id' => $store->getId(),
                'name' => $store->getName(),
                'filename' => $this->getFileName($store->getBaseUrl(), $store->getCode()),
                'created_at' => $this->getExportedAt($store->getCode()),
                'action' => $this->getExportAction($store->getId())
            ];
            $dataObject = new DataObject();
            $dataObject->setData($item);
            $this->_items[] = $dataObject;
        }
    }

    protected function getFileName($url, $code)
    {
        $exportFile = ExportProductsTask::EXPORT_FILE;

        $path = 'pub' . DS . ExportProductsTask::EXPORT_BASEDIR . DS .
            ExportProductsTask::EXPORT_FOLDER . DS;

        $fileName = sprintf($exportFile, $code);
        $fileUrl = $url . $path . $fileName;

        return $fileUrl;
    }

    protected function getExportedAt($code)
    {
        $filePath = $this->directoryList->getPath(ExportProductsTask::EXPORT_BASEDIR)
            . DS . ExportProductsTask::EXPORT_FOLDER . DS .
            sprintf(ExportProductsTask::EXPORT_FILE, $code);

        if (!file_exists($filePath)) {
            return '';
        }

        return date("Y-m-d H:i:s", filemtime($filePath));
    }

    protected function getExportAction($id)
    {
        $exportUrl = $this->context->getUrl('check24shopping_orderimport/export/product', ['id' => $id]);

        return sprintf('<a href="%s">Export</a>', $exportUrl);
    }

    public function getItems()
    {

        return $this->_items;
    }
}
