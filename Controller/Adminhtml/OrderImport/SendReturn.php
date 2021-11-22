<?php

namespace Check24Shopping\OrderImport\Controller\Adminhtml\OrderImport;

use Check24Shopping\OrderImport\Model\Exception\CustomerMessageInterface;
use Check24Shopping\OrderImport\Model\Task\ProcessReturnTask;
use Exception;
use Magento\Backend\App\Action;

class SendReturn extends Action
{
    /** @var ProcessReturnTask */
    private $returnTask;

    public function __construct(
        Action\Context    $context,
        ProcessReturnTask $returnTask
    )
    {
        parent::__construct($context);
        $this->returnTask = $returnTask;
    }

    public function execute()
    {
        try {
            $returnTaskResult = $this->returnTask->sendNotSendReturns();
            $failed = $returnTaskResult->getAmountFailedOrders();
            $this
                ->messageManager
                ->addNoticeMessage(
                    $returnTaskResult->getAmountProcessedOrders() . ' send. ' .
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
