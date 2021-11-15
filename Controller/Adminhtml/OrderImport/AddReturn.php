<?php

namespace Check24Shopping\OrderImport\Controller\Adminhtml\OrderImport;

use Check24Shopping\OrderImport\Api\Check24ReturnRepositoryInterface;
use Check24Shopping\OrderImport\Api\Data\Check24ReturnInterfaceFactory;
use Exception;
use Magento\Backend\App\Action;

class AddReturn extends Action
{
    /**
     * @var Action\Context
     */
    private $context;
    /**
     * @var Check24ReturnInterfaceFactory
     */
    private $returnFactory;
    /**
     * @var Check24ReturnRepositoryInterface
     */
    private $returnRepository;

    public function __construct(
        Action\Context                   $context,
        Check24ReturnInterfaceFactory    $returnFactory,
        Check24ReturnRepositoryInterface $returnRepository
    )
    {
        parent::__construct($context);
        $this->context = $context;
        $this->returnFactory = $returnFactory;
        $this->returnRepository = $returnRepository;
    }

    public function execute()
    {
        $orderId = $this->context->getRequest()->getParam('orderId');
        try {
            $this
                ->addReturn($orderId);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->_redirect('sales/order/view', ['order_id' => $orderId]);
    }

    private function addReturn(string $orderId)
    {
        $return = $this->returnRepository->findByOrderId($orderId);
        if (empty($return) === false) {
            return;
        }

        $return = $this
            ->returnFactory
            ->create()
            ->setOrderId($orderId);

        $this
            ->returnRepository
            ->save(
                $return
            );
    }
}
