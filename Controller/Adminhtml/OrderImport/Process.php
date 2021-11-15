<?php

namespace Check24Shopping\OrderImport\Controller\Adminhtml\OrderImport;

use Check24Shopping\OrderImport\Model\Exception\CustomerMessageInterface;
use Check24Shopping\OrderImport\Model\Task\ProcessOrderTask;
use Exception;
use Magento\Backend\App\Action;

class Process extends Action
{
    /** @var ProcessOrderTask */
    private $processOrderTask;

    public function __construct(
        Action\Context   $context,
        ProcessOrderTask $processOrderTask
    )
    {
        parent::__construct($context);
        $this->processOrderTask = $processOrderTask;
    }

    public function execute(): void
    {
        try {
            $processOrderResult = $this->processOrderTask->processNotProcessedOrders();
            $failed = $processOrderResult->getAmountFailedOrders();
            $this
                ->messageManager
                ->addNoticeMessage(
                    'Process Orders finished. ' .
                    $processOrderResult->getAmountProcessedOrders() . ' processed. ' .
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
