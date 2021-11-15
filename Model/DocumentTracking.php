<?php

namespace Check24\OrderImport\Model;

use Check24\OrderImport\Api\Data\DocumentTrackingInterface;
use Check24\OrderImport\Model\ResourceModel\DocumentTracking as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class DocumentTracking extends AbstractModel implements DocumentTrackingInterface
{
    /** @var string */
    const CACHE_TAG = DocumentTrackingInterface::TABLE_NAME;

    /** @var string */
    protected $_cacheTag = DocumentTrackingInterface::TABLE_NAME;

    /** @var string */
    protected $_eventPrefix = DocumentTrackingInterface::TABLE_NAME;

    public function setId($id): DocumentTrackingInterface
    {
        $this->setData(self::FIELD_ID, $id);

        return $this;
    }

    public function getId()
    {
        return $this->getData(DocumentTrackingInterface::FIELD_ID);
    }

    public function setDocumentId(int $documentId): DocumentTrackingInterface
    {
        $this->setData(self::FIELD_DOCUMENT_ID, $documentId);

        return $this;
    }

    public function getDocumentId()
    {
        return $this->getData(self::FIELD_DOCUMENT_ID);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
