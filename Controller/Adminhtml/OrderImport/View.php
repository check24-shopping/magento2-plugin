<?php

namespace Check24\OrderImport\Controller\Adminhtml\OrderImport;

use Check24\OrderImport\Api\OrderImportRepositoryInterface;
use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class View extends Action
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
                ->setHeader('Content-Type', 'text/xml')
                ->setContents($order->getContent());

            return $resultPage;


        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            $this->_redirect('*/*/index');
        }
    }

}
