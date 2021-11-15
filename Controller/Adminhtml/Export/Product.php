<?php

namespace Check24Shopping\OrderImport\Controller\Adminhtml\Export;

use Check24Shopping\OrderImport\Model\Task\ExportProductsTask;
use Exception;
use Magento\Backend\App\Action;

class Product extends Action
{
    /** @var ExportProductsTask */
    private $exportProductsTask;

    public function __construct(
        Action\Context     $context,
        ExportProductsTask $exportProductsTask
    )
    {
        parent::__construct($context);
        $this->exportProductsTask = $exportProductsTask;
    }

    public function execute()
    {
        try {
            $this->exportProductsTask->exportProducts($this->getRequest()->getParam('id'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Check Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Check24_OrderImport::productexport');
    }
}
