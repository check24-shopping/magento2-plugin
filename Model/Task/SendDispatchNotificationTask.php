<?php

namespace Check24\OrderImport\Model\Task;

use Check24\OrderImport\Api\Check24ShipmentProviderInterface;
use Check24\OrderImport\Api\Check24ShipmentRepositoryInterface;
use Check24\OrderImport\Api\Data\Check24ShipmentInterface;
use Check24\OrderImport\Api\OrderManagementInterface;
use Check24\OrderImport\Helper\Config\ApiConfiguration;
use Check24\OrderImport\Model\OrderMappingRepository;
use Check24\OrderImport\Model\OrderPositionMappingRepository;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransAddressEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransDelivererPartyEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransDeliveryPartyEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransDispatchOrderEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransInvoicePartyEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransOrderItemEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransSupplierPartyEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataDispatchOrderInterface;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderItemCollection;
use Check24\OrderImport\Model\Task\Model\ProcessOrderResult;
use Check24\OrderImport\Model\Writer\OpenTrans\OpenTransDispatchNotification;
use Check24\OrderImport\Service\Api\ApiConfigurationService;
use Check24\OrderImport\Service\Api\DispatchNotificationService;
use Exception;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class SendDispatchNotificationTask
{

    /** @var Check24ShipmentProviderInterface */
    private $check24ShipmentProvider;
    /** @var Check24ShipmentRepositoryInterface */
    private $check24ShipmentRepository;
    /** @var StoreManagerInterface */
    private $storeManager;
    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;
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
     * @var DispatchNotificationService
     */
    private $dispatchNotificationService;
    /**
     * @var ApiConfigurationService
     */
    private $apiConfigurationService;

    public function __construct(
        Check24ShipmentProviderInterface   $check24ShipmentProvider,
        Check24ShipmentRepositoryInterface $check24ShipmentRepository,
        StoreManagerInterface              $storeManager,
        ShipmentRepositoryInterface        $shipmentRepository,
        OrderMappingRepository             $orderMappingRepository,
        OrderManagementInterface           $orderManagement,
        OrderPositionMappingRepository     $orderPositionMappingRepository,
        DispatchNotificationService        $dispatchNotificationService,
        ApiConfigurationService            $apiConfigurationService
    )
    {
        $this->check24ShipmentProvider = $check24ShipmentProvider;
        $this->check24ShipmentRepository = $check24ShipmentRepository;
        $this->storeManager = $storeManager;
        $this->shipmentRepository = $shipmentRepository;
        $this->orderMappingRepository = $orderMappingRepository;
        $this->orderPositionMappingRepository = $orderPositionMappingRepository;
        $this->orderManagement = $orderManagement;
        $this->dispatchNotificationService = $dispatchNotificationService;
        $this->apiConfigurationService = $apiConfigurationService;
    }

    public function submit(): ProcessOrderResult
    {
        $check24Shipments = $this->check24ShipmentProvider->getNotSubmitted();
        if (empty($check24Shipments->getTotalCount())) {
            return new ProcessOrderResult(0, 0);
        }
        $ordersProcessed = $failedOrders = 0;
        /** @var Check24ShipmentInterface $check24Shipment */
        foreach ($check24Shipments->getItems() as $check24Shipment) {
            try {
                $shipment = $this->shipmentRepository->get($check24Shipment->getShipmentId());

                $itemCollection = new OpenTransDataOrderItemCollection();
                foreach ($shipment->getItems() as $item) {
                    $mappingItem = $this
                        ->orderPositionMappingRepository
                        ->findByMagentoPositionId($item->getOrderItemId());
                    $openTransItem = new OpenTransOrderItemEntity(
                        $item->getSku(),
                        (int)$item->getQty(),
                        $item->getPrice(),
                        '',
                        $mappingItem->getCheck24PositionId(),
                        $mappingItem->getOrderUnit()
                    );
                    $itemCollection->add($openTransItem);
                }
                $mappingOrder = $this->orderMappingRepository->findByOrderId($shipment->getOrderId());

                foreach ($shipment->getTracks() as $track) {
                    $dispatchOrderEntity = new OpenTransDispatchOrderEntity(
                        $shipment->getEntityId() . '-' . $track->getEntityId(),
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
                        ),
                        new OpenTransDelivererPartyEntity(
                            $track->getCarrierCode(),
                            new OpenTransAddressEntity()
                        ),
                        $track->getTrackNumber()
                    );
                    $storeId = $this->orderManagement->getStoreIdByPartnerId($dispatchOrderEntity->getPartnerId());
                    $this->sendDispatchNotification(
                        $this->apiConfigurationService->findStoreApiConfiguration($storeId),
                        $dispatchOrderEntity
                    );
                }
                $ordersProcessed++;
                $check24Shipment->setStatus(1);
            } catch (Exception $e) {
                $failedOrders++;
                $check24Shipment
                    ->setErrorMessage($e->getMessage());
            } finally {
                $this->check24ShipmentRepository->save($check24Shipment);
            }
        }

        return new ProcessOrderResult($ordersProcessed, $failedOrders);
    }

    private function sendDispatchNotification(
        ApiConfiguration                    $apiConfiguration,
        OpenTransDataDispatchOrderInterface $document
    )
    {
        $this
            ->dispatchNotificationService
            ->response($apiConfiguration, new OpenTransDispatchNotification($document));
    }
}
