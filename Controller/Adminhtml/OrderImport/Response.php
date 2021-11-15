<?php

namespace Check24Shopping\OrderImport\Controller\Adminhtml\OrderImport;

use Check24Shopping\OrderImport\Model\Exception\CustomerMessageInterface;
use Check24Shopping\OrderImport\Model\Task\SendOrderResponseTask;
use Exception;
use Magento\Backend\App\Action;

class Response extends Action
{
    /**
     * @var SendOrderResponseTask
     */
    private $sendOrderResponseTask;

    public function __construct(
        Action\Context        $context,
        SendOrderResponseTask $sendOrderResponseTask
    )
    {
        parent::__construct($context);
        $this->sendOrderResponseTask = $sendOrderResponseTask;
    }

    public function execute(): void
    {
        try {
            $processOrderResult = $this->sendOrderResponseTask->sendNotRespondedOrders();
            $failed = $processOrderResult->getAmountFailedOrders();
            $this
                ->messageManager
                ->addNoticeMessage(
                    'Response orders finished. ' .
                    $processOrderResult->getAmountProcessedOrders() . ' responded. ' .
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
