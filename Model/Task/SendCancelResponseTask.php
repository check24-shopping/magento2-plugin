<?php

namespace Check24\OrderImport\Model\Task;

use Check24\OrderImport\Api\Check24CancelProviderInterface;
use Check24\OrderImport\Api\Check24CancelRepositoryInterface;
use Check24\OrderImport\Api\Data\Check24CancelInterface;
use Check24\OrderImport\Api\OrderManagementInterface;
use Check24\OrderImport\Helper\Config\ApiConfiguration;
use Check24\OrderImport\Model\OrderMappingRepository;
use Check24\OrderImport\Model\OrderPositionMappingRepository;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransAddressEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransCancelResponseEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransDeliveryPartyEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransInvoicePartyEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransOrderItemEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransSupplierPartyEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataCancelResponseInterface;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderItemCollection;
use Check24\OrderImport\Model\Task\Model\ProcessOrderResult;
use Check24\OrderImport\Model\Writer\OpenTrans\OpenTransCancelResponse;
use Check24\OrderImport\Service\Api\ApiConfigurationService;
use Check24\OrderImport\Service\Api\CancelResponseService;
use Exception;
use Magento\Sales\Api\CancelRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class SendCancelResponseTask
{

    /** @var Check24CancelProviderInterface */
    private $check24CancelProvider;
    /** @var Check24CancelRepositoryInterface */
    private $check24CancelRepository;
    /** @var StoreManagerInterface */
    private $storeManager;
    /**
     * @var OrderMappingRepository
     */
    private $orderMappingRepository;
    /**
     * @var OrderPositionMappingRepository
     */
    private $orderPositionMappingRepository;
    /**
     * @var OrderManagementInterface
     */
    private $orderManagement;
    /**
     * @var CancelResponseService
     */
    private $cancelResponseService;
    /**
     * @var ApiConfigurationService
     */
    private $apiConfigurationService;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    public function __construct(
        Check24CancelProviderInterface   $check24CancelProvider,
        Check24CancelRepositoryInterface $check24CancelRepository,
        StoreManagerInterface            $storeManager,
        OrderMappingRepository           $orderMappingRepository,
        OrderManagementInterface         $orderManagement,
        OrderPositionMappingRepository   $orderPositionMappingRepository,
        CancelResponseService            $cancelResponseService,
        ApiConfigurationService          $apiConfigurationService,
        OrderRepositoryInterface         $orderRepository
    )
    {
        $this->check24CancelProvider = $check24CancelProvider;
        $this->check24CancelRepository = $check24CancelRepository;
        $this->storeManager = $storeManager;
        $this->orderMappingRepository = $orderMappingRepository;
        $this->orderPositionMappingRepository = $orderPositionMappingRepository;
        $this->orderManagement = $orderManagement;
        $this->cancelResponseService = $cancelResponseService;
        $this->apiConfigurationService = $apiConfigurationService;
        $this->orderRepository = $orderRepository;
    }

    public function submit(): ProcessOrderResult
    {
        $check24Cancels = $this->check24CancelProvider->getNotSubmitted();
        if (empty($check24Cancels->getTotalCount())) {
            return new ProcessOrderResult(0, 0);
        }
        $ordersProcessed = $failedOrders = 0;
        /** @var Check24CancelInterface $check24Cancel */
        foreach ($check24Cancels->getItems() as $check24Cancel) {
            try {
                $magentoOrder = $this->orderRepository->get($check24Cancel->getOrderId());

                $itemCollection = new OpenTransDataOrderItemCollection();
                foreach ($magentoOrder->getItems() as $item) {
                    $mappingItem = $this
                        ->orderPositionMappingRepository
                        ->findByMagentoPositionId($item->getItemId());
                    $openTransItem = new OpenTransOrderItemEntity(
                        $item->getSku(),
                        -1 * (int)$item->getQtyCanceled(),
                        $item->getPrice(),
                        '',
                        $mappingItem->getCheck24PositionId(),
                        $mappingItem->getOrderUnit()
                    );
                    $itemCollection->add($openTransItem);
                }
                $mappingOrder = $this->orderMappingRepository->findByOrderId($check24Cancel->getOrderId());

                $openTransCancelResponseEntity = new OpenTransCancelResponseEntity(
                    'cancel-' . $mappingOrder->getCheck24OrderId(),
                    $mappingOrder->getPartnerId(),
                    $mappingOrder->getCheck24OrderId(),
                    0.0,
                    $itemCollection,
                    new OpenTransInvoicePartyEntity(
                        $mappingOrder->getPartyInvoiceIssuerId(),
                        new OpenTransAddressEntity()
                    ),
                    new OpenTransDeliveryPartyEntity(
                        $mappingOrder->getPartyDeliveryId(),
                        new OpenTransAddressEntity()
                    ),
                    new OpenTransSupplierPartyEntity(
                        $mappingOrder->getPartySupplierId(),
                        new OpenTransAddressEntity()
                    )
                );
                $storeId = $this->orderManagement->getStoreIdByPartnerId($openTransCancelResponseEntity->getPartnerId());
                $this->sendCancelResponse(
                    $this->apiConfigurationService->findStoreApiConfiguration($storeId),
                    $openTransCancelResponseEntity
                );
                $ordersProcessed++;
                $check24Cancel->setStatus(1);
            } catch (Exception $e) {
                $failedOrders++;
                $check24Cancel
                    ->setErrorMessage($e->getMessage());
            } finally {
                $this->check24CancelRepository->save($check24Cancel);
            }
        }

        return new ProcessOrderResult($ordersProcessed, $failedOrders);
    }

    private function sendCancelResponse(
        ApiConfiguration                     $apiConfiguration,
        OpenTransDataCancelResponseInterface $document
    )
    {
        $this
            ->cancelResponseService
            ->response($apiConfiguration, new OpenTransCancelResponse($document));
    }
}
