<?php

namespace Check24Shopping\OrderImport\Controller\Adminhtml\OrderImport;

use Check24Shopping\OrderImport\Model\Exception\CustomerMessageInterface;
use Check24Shopping\OrderImport\Model\Task\ProcessReturnTask;
use Check24Shopping\OrderImport\Model\Task\ReturnRequestTask;
use Exception;
use Magento\Backend\App\Action;

class ProcessReturnRequest extends Action
{
    /** @var ReturnRequestTask */
    private $returnRequestTask;

    public function __construct(
        Action\Context    $context,
        ReturnRequestTask $returnRequestTask
    )
    {
        parent::__construct($context);
        $this->returnRequestTask = $returnRequestTask;
    }

    public function execute()
    {
        try {
            $returnTaskResult = $this->returnRequestTask->processNotReturnRequest();
            $failed = $returnTaskResult->getAmountFailedOrders();
            $this
                ->messageManager
                ->addNoticeMessage(
                    $returnTaskResult->getAmountProcessedOrders() . ' processed. ' .
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
