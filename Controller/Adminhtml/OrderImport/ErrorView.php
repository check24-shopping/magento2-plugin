<?php

namespace Check24Shopping\OrderImport\Controller\Adminhtml\OrderImport;

use Check24Shopping\OrderImport\Api\OrderImportRepositoryInterface;
use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class ErrorView extends Action
{
    /** @var OrderImportRepositoryInterface */
    private $orderRepository;

    public function __construct(
        Action\Context                 $context,
        OrderImportRepositoryInterface $orderRepository
    )
    {
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
    }

    public function execute()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            $order = $this->orderRepository->load($id);
            if (!$order->getId() > 0) {
                throw new NoSuchEntityException(__(sprintf('order id %d not found', $id)));
            }

            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_RAW);
            $resultPage
                ->setHeader('Content-Type', 'text/html')
                ->setContents(
                    '<pre>' . $order->getErrorDetails() . '</pre>'
                );

            return $resultPage;


        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $this->_redirect('*/*/index');
        }
    }

}
