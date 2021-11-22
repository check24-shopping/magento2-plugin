<?php

namespace Check24Shopping\OrderImport\Controller\Adminhtml\OrderImport;

use Check24Shopping\OrderImport\Api\Check24ReturnRepositoryInterface;
use Check24Shopping\OrderImport\Api\Data\Check24ReturnInterfaceFactory;
use Check24Shopping\OrderImport\Api\Data\Check24ShipmentInterface;
use Check24Shopping\OrderImport\Api\Data\ReturnRequestInterface;
use Check24Shopping\OrderImport\Api\ReturnRequestRepositoryInterface;
use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\Api\SearchCriteriaBuilder;

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
    /**
     * @var ReturnRequestRepositoryInterface
     */
    private $returnRequestRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        Action\Context                   $context,
        Check24ReturnInterfaceFactory    $returnFactory,
        Check24ReturnRepositoryInterface $returnRepository,
        ReturnRequestRepositoryInterface $returnRequestRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        parent::__construct($context);
        $this->context = $context;
        $this->returnFactory = $returnFactory;
        $this->returnRepository = $returnRepository;
        $this->returnRequestRepository = $returnRequestRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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

        $searchCriteria = $this
            ->searchCriteriaBuilder
            ->addFilter(ReturnRequestInterface::FIELD_MAGENTO_ORDER_ID, $orderId)
            ->create();

        $returnRequests = $this
            ->returnRequestRepository
            ->getList($searchCriteria);
        if(empty($returnRequests) === false){
            $returnRequest = reset($returnRequests);
            $this
                ->returnRequestRepository
                ->delete($returnRequest);
        }
    }
}
