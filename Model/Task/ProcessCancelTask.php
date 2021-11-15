<?php

namespace Check24\OrderImport\Model\Task;

use Check24\OrderImport\Api\Data\OrderImportInterface;
use Check24\OrderImport\Api\OrderImportProviderInterface;
use Check24\OrderImport\Api\OrderImportRepositoryInterface;
use Check24\OrderImport\Helper\Config\OrderConfig;
use Check24\OrderImport\Model\Exception\NoOrderMappingFound;
use Check24\OrderImport\Model\OrderMappingRepository;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDocumentInterface;
use Check24\OrderImport\Model\Reader\OpenTrans\Xml\OpenTransOrderDocument;
use Check24\OrderImport\Model\Task\Model\ProcessOrderResult;
use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\OrderManagementInterface as MagentoOrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Order;

class ProcessCancelTask
{
    /** @var OrderImportProviderInterface */
    private $orderProvider;
    /** @var OrderConfig */
    private $orderConfig;
    /** @var MagentoOrderManagementInterface */
    private $magentoOrderManagement;
    /** @var OrderImportRepositoryInterface */
    private $orderRepository;
    /** @var OrderMappingRepository */
    private $orderMappingRepository;
    /** @var ShipmentRepositoryInterface */
    private $shipmentRepository;
    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;
    /**
     * @var OrderRepositoryInterface
     */
    private $magentoOrderRepository;

    public function __construct(
        OrderImportProviderInterface    $orderProvider,
        OrderImportRepositoryInterface  $orderRepository,
        OrderConfig                     $orderConfig,
        MagentoOrderManagementInterface $magentoOrderManagement,
        ShipmentRepositoryInterface     $shipmentRepository,
        SearchCriteriaBuilder           $searchCriteriaBuilder,
        OrderMappingRepository          $orderMappingRepository,
        OrderRepositoryInterface $magentoOrderRepository
    )
    {
        $this->orderProvider = $orderProvider;
        $this->orderConfig = $orderConfig;
        $this->magentoOrderManagement = $magentoOrderManagement;
        $this->orderRepository = $orderRepository;
        $this->orderMappingRepository = $orderMappingRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->magentoOrderRepository = $magentoOrderRepository;
    }

    public function processNotProcessedOrders(): ProcessOrderResult
    {
        $orderList = $this->orderProvider->getCancelList();
        if (empty($orderList->getTotalCount())) {
            return new ProcessOrderResult(0, 0);
        }
        $ordersProcessed = $failedOrders = 0;
        /** @var OrderImportInterface $order */
        foreach ($orderList->getItems() as $order) {
            try {
                $document = new OpenTransOrderDocument($order->getContent());
                if ($document->getAction() !== OpenTransDocumentInterface::ACTION_CANCELLATION_REQUEST) {
                    continue;
                }
                $mappingOrder = $this->orderMappingRepository->findByCheck24OrderId($order->getCheck24OrderId());
                if (empty($mappingOrder)) {
                    throw new NoOrderMappingFound();
                }
                $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter('order_id', $mappingOrder->getMagentoOrderId())->create();
                $shipmentSearchResult = $this->shipmentRepository->getList($searchCriteria);
                if ($shipmentSearchResult->getTotalCount() === 0 &&
                    $this->magentoOrderManagement->cancel($mappingOrder->getMagentoOrderId())
                ) {
                    $ordersProcessed++;
                    /** @var Order $magentoOrder */
                    $magentoOrder = $this
                        ->magentoOrderRepository
                        ->get($mappingOrder->getMagentoOrderId());
                    $magentoOrder
                        ->addCommentToStatusHistory(
                            'Order cancelled by CHECK24 request',
                        );
                    $this->magentoOrderRepository->save($magentoOrder);
                } else {
                    $failedOrders++;
                }
                $this->orderRepository->delete($order);
            } catch (Exception $e) {
                $failedOrders++;
                $order
                    ->setErrorMessage($e->getMessage());
                $this->orderRepository->save($order);
            }
        }

        return new ProcessOrderResult($ordersProcessed, $failedOrders);
    }
}
