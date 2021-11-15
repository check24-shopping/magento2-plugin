<?php

namespace Check24Shopping\OrderImport\Controller\Adminhtml\OrderImport;

use Check24Shopping\OrderImport\Model\Exception\CustomerMessageInterface;
use Check24Shopping\OrderImport\Model\Task\SendDispatchNotificationTask;
use Exception;
use Magento\Backend\App\Action;

class Dispatch extends Action
{
    /** @var SendDispatchNotificationTask */
    private $dispatchNotificationTask;

    public function __construct(
        Action\Context               $context,
        SendDispatchNotificationTask $dispatchNotificationTask
    )
    {
        parent::__construct($context);
        $this->dispatchNotificationTask = $dispatchNotificationTask;
    }

    public function execute(): void
    {
        try {
            $processOrderResult = $this->dispatchNotificationTask->submit();
            $failed = $processOrderResult->getAmountFailedOrders();
            $this
                ->messageManager
                ->addNoticeMessage(
                    $processOrderResult->getAmountProcessedOrders() . ' dispatched. ' .
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
