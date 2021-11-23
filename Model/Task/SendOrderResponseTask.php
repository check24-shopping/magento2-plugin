<?php

namespace Check24Shopping\OrderImport\Model\Task;

use Check24Shopping\OrderImport\Api\OrderImportProviderInterface;
use Check24Shopping\OrderImport\Api\OrderImportRepositoryInterface;
use Check24Shopping\OrderImport\Api\OrderManagementInterface;
use Check24Shopping\OrderImport\Helper\Config\ApiConfiguration;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderInterface;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\OpenTransDocumentInterface;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\Xml\OpenTransOrderDocument;
use Check24Shopping\OrderImport\Model\Task\Model\ProcessOrderResult;
use Check24Shopping\OrderImport\Model\Writer\OpenTrans\OpenTransCancelResponse;
use Check24Shopping\OrderImport\Model\Writer\OpenTrans\OpenTransOrderResponse;
use Check24Shopping\OrderImport\Service\Api\ApiConfigurationService;
use Check24Shopping\OrderImport\Service\Api\CancelResponseService;
use Check24Shopping\OrderImport\Service\Api\OrderResponseService;
use Exception;

class SendOrderResponseTask
{
    /** @var OrderImportProviderInterface */
    private $orderProvider;
    /** @var OrderManagementInterface */
    private $orderManagement;
    /** @var OrderImportRepositoryInterface */
    private $orderRepository;
    /** @var OrderResponseService */
    private $orderResponseService;
    /**
     * @var ApiConfigurationService
     */
    private $apiConfigurationService;
    /**
     * @var CancelResponseService
     */
    private $cancelResponseService;

    public function __construct(
        OrderImportProviderInterface   $orderProvider,
        OrderManagementInterface       $orderManagement,
        OrderImportRepositoryInterface $orderRepository,
        OrderResponseService           $orderResponseService,
        ApiConfigurationService        $apiConfigurationService,
        CancelResponseService          $cancelResponseService
    )
    {
        $this->orderProvider = $orderProvider;
        $this->orderManagement = $orderManagement;
        $this->orderRepository = $orderRepository;
        $this->orderResponseService = $orderResponseService;
        $this->apiConfigurationService = $apiConfigurationService;
        $this->cancelResponseService = $cancelResponseService;
    }

    public function sendNotRespondedOrders(): ProcessOrderResult
    {
        $orderList = $this->orderProvider->getNotRespondedList();
        if (empty($orderList->getTotalCount())) {
            return new ProcessOrderResult(0, 0);
        }
        $ordersProcessed = $failedOrders = 0;
        foreach ($orderList->getItems() as $order) {
            try {
                $document = new OpenTransOrderDocument($order->getContent());
                $storeId = $this->orderManagement->getStoreIdByPartnerId($document->getPartnerId());
                if ($document->getAction() === OpenTransDocumentInterface::ACTION_CANCELLATION_REQUEST) {
                    $this
                        ->sendCancelResponse(
                            $this->apiConfigurationService->findStoreApiConfiguration($storeId),
                            $document
                        );
                } else {
                    $this->sendOrderResponse(
                        $this->apiConfigurationService->findStoreApiConfiguration($storeId),
                        $document
                    );
                }
                $this->orderRepository->delete($order);
                $ordersProcessed++;
            } catch (Exception $e) {
                $failedOrders++;
                $order
                    ->setErrorMessage($e->getMessage());
                $this->orderRepository->save($order);
            }
        }

        return new ProcessOrderResult($ordersProcessed, $failedOrders);
    }

    private function sendOrderResponse(ApiConfiguration $apiConfiguration, OpenTransDataOrderInterface $document)
    {
        $this
            ->orderResponseService
            ->response($apiConfiguration, new OpenTransOrderResponse($document));
    }

    private function sendCancelResponse(ApiConfiguration $apiConfiguration, OpenTransDataOrderInterface $document)
    {
        $this
            ->cancelResponseService
            ->response($apiConfiguration, new OpenTransCancelResponse($document));
    }
}
