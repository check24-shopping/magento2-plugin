<?php

namespace Check24Shopping\OrderImport\Controller\Adminhtml\OrderImport;

use Check24Shopping\OrderImport\Model\Exception\CustomerMessageInterface;
use Check24Shopping\OrderImport\Model\Task\ImportOrderTask;
use Exception;
use Magento\Backend\App\Action;

class Import extends Action
{
    /** @var ImportOrderTask */
    private $importOrderTask;

    public function __construct(
        Action\Context  $context,
        ImportOrderTask $importOrderTask
    )
    {
        parent::__construct($context);
        $this->importOrderTask = $importOrderTask;
    }

    public function execute()
    {
        try {
            $importOrderResult = $this->importOrderTask->execute();
            $skipped = $importOrderResult->getAmountSkippedOrders();
            $this
                ->messageManager
                ->addNoticeMessage(
                    'Manual Import successful. ' .
                    $importOrderResult->getAmountImportedOrders() . ' imported. ' .
                    ($skipped ? $skipped . ' skipped.' : '')
                );
        } catch (CustomerMessageInterface $exception) {
            $this->messageManager->addErrorMessage($exception->getCustomerMessage());
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->_redirect('*/*/index');
    }
}
