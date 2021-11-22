<?php

namespace Check24Shopping\OrderImport\Controller\Adminhtml\ReturnRequest;

use Check24Shopping\OrderImport\Model\Exception\CustomerMessageInterface;
use Check24Shopping\OrderImport\Model\Task\ProcessCancelTask;
use Exception;
use Magento\Backend\App\Action;

class ReturnRequest extends Action
{
    /** @var ProcessCancelTask */
    private $processCancelTask;

    public function __construct(
        Action\Context    $context,
        ProcessCancelTask $processCancelTask
    )
    {
        parent::__construct($context);
        $this->processCancelTask = $processCancelTask;
    }

    public function execute(): void
    {
        try {
            $processOrderResult = $this->processCancelTask->processNotProcessedOrders();
            $failed = $processOrderResult->getAmountFailedOrders();
            $this
                ->messageManager
                ->addNoticeMessage(
                    'Cancel response finished. ' .
                    $processOrderResult->getAmountProcessedOrders() . ' submitted. ' .
                    ($failed ? $failed . ' failed.' : '')
                );
        } catch (CustomerMessageInterface $exception) {
            $this->messageManager->addErrorMessage($exception->getCustomerMessage());
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->_redirect('*/*/index');
    }
}
