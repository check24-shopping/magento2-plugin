<?php

namespace Check24\OrderImport\Controller\Adminhtml\OrderImport;

use Check24\OrderImport\Model\Exception\CustomerMessageInterface;
use Check24\OrderImport\Model\Task\SendCancelResponseTask;
use Exception;
use Magento\Backend\App\Action;

class CancelResponse extends Action
{
    /** @var SendCancelResponseTask */
    private $sendCancelResponseTask;

    public function __construct(
        Action\Context         $context,
        SendCancelResponseTask $sendCancelResponseTask
    )
    {
        parent::__construct($context);
        $this->sendCancelResponseTask = $sendCancelResponseTask;
    }

    public function execute(): void
    {
        try {
            $processOrderResult = $this->sendCancelResponseTask->submit();
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
