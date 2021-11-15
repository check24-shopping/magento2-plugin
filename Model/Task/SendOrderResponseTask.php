<?php

namespace Check24\OrderImport\Model\Task;

use Check24\OrderImport\Api\OrderImportProviderInterface;
use Check24\OrderImport\Api\OrderImportRepositoryInterface;
use Check24\OrderImport\Api\OrderManagementInterface;
use Check24\OrderImport\Helper\Config\ApiConfiguration;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderInterface;
use Check24\OrderImport\Model\Reader\OpenTrans\Xml\OpenTransOrderDocument;
use Check24\OrderImport\Model\Task\Model\ProcessOrderResult;
use Check24\OrderImport\Model\Writer\OpenTrans\OpenTransOrderResponse;
use Check24\OrderImport\Service\Api\ApiConfigurationService;
use Check24\OrderImport\Service\Api\OrderResponseService;
use Exception;
use Magento\Quote\Api\CartManagementInterface;

class SendOrderResponseTask
{
    /** @var OrderImportProviderInterface */
    private $orderProvider;
    /** @var OrderManagementInterface */
    private $orderManagement;
    /** @var CartManagementInterface */
    private $cartManagement;
    /** @var OrderImportRepositoryInterface */
    private $orderRepository;
    /** @var OrderResponseService */
    private $orderResponseService;
    /**
     * @var ApiConfigurationService
     */
    private $apiConfigurationService;

    public function __construct(
        OrderImportProviderInterface   $orderProvider,
        OrderManagementInterface       $orderManagement,
        OrderImportRepositoryInterface $orderRepository,
        CartManagementInterface        $cartManagement,
        OrderResponseService           $orderResponseService,
        ApiConfigurationService        $apiConfigurationService
    )
    {
        $this->orderProvider = $orderProvider;
        $this->orderManagement = $orderManagement;
        $this->cartManagement = $cartManagement;
        $this->orderRepository = $orderRepository;
        $this->orderResponseService = $orderResponseService;
        $this->apiConfigurationService = $apiConfigurationService;
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
                $this->sendOrderResponse(
                    $this->apiConfigurationService->findStoreApiConfiguration($storeId),
                    $document
                );
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
}
