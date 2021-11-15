<?php

namespace Check24\OrderImport\Model\Task;

use Check24\OrderImport\Api\Check24ReturnProviderInterface;
use Check24\OrderImport\Api\Check24ReturnRepositoryInterface;
use Check24\OrderImport\Api\Data\Check24ReturnInterface;
use Check24\OrderImport\Api\OrderManagementInterface;
use Check24\OrderImport\Helper\Config\ApiConfiguration;
use Check24\OrderImport\Helper\Config\OrderConfig;
use Check24\OrderImport\Model\OrderMappingRepository;
use Check24\OrderImport\Model\OrderPositionMappingRepository;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransAddressEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransDeliveryPartyEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransInvoicePartyEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransOrderItemEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransReturnResponseEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\Entity\OpenTransSupplierPartyEntity;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderItemCollection;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataReturnResponseInterface;
use Check24\OrderImport\Model\Task\Model\ProcessOrderResult;
use Check24\OrderImport\Model\Writer\OpenTrans\OpenTransReturnResponse;
use Check24\OrderImport\Service\Api\ApiConfigurationService;
use Check24\OrderImport\Service\Api\ReturnResponseService;
use Exception;
use Magento\Sales\Api\OrderManagementInterface as MagentoOrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class ProcessReturnTask
{
    /** @var OrderConfig */
    private $orderConfig;
    /** @var MagentoOrderManagementInterface */
    private $magentoOrderManagement;
    /** @var OrderRepositoryInterface */
    private $orderRepository;
    /** @var OrderMappingRepository */
    private $orderMappingRepository;
    /** @var Check24ReturnRepositoryInterface */
    private $returnRepository;
    /** @var Check24ReturnProviderInterface */
    private $check24ReturnProvider;
    /** @var OrderPositionMappingRepository */
    private $orderPositionMappingRepository;
    /**
     * @var ApiConfigurationService
     */
    private $apiConfigurationService;
    /**
     * @var OrderManagementInterface
     */
    private $orderManagement;
    /**
     * @var ReturnResponseService
     */
    private $returnResponseService;

    public function __construct(
        OrderRepositoryInterface         $orderRepository,
        OrderConfig                      $orderConfig,
        MagentoOrderManagementInterface  $magentoOrderManagement,
        OrderMappingRepository           $orderMappingRepository,
        OrderPositionMappingRepository   $orderPositionMappingRepository,
        Check24ReturnRepositoryInterface $returnRepository,
        ApiConfigurationService          $apiConfigurationService,
        OrderManagementInterface         $orderManagement,
        Check24ReturnProviderInterface   $check24ReturnProvider,
        ReturnResponseService            $returnResponseService
    )
    {
        $this->orderConfig = $orderConfig;
        $this->magentoOrderManagement = $magentoOrderManagement;
        $this->orderRepository = $orderRepository;
        $this->orderMappingRepository = $orderMappingRepository;
        $this->returnRepository = $returnRepository;
        $this->check24ReturnProvider = $check24ReturnProvider;
        $this->orderPositionMappingRepository = $orderPositionMappingRepository;
        $this->apiConfigurationService = $apiConfigurationService;
        $this->orderManagement = $orderManagement;
        $this->returnResponseService = $returnResponseService;
    }

    public function sendNotSendReturns(): ProcessOrderResult
    {
        $check24Returns = $this->check24ReturnProvider->getNotSubmitted();
        if (empty($check24Returns->getTotalCount())) {
            return new ProcessOrderResult(0, 0);
        }
        $ordersProcessed = $failedOrders = 0;
        /** @var Check24ReturnInterface $check24Return */
        foreach ($check24Returns->getItems() as $check24Return) {
            try {
                /** @var Order $magentoOrder */
                $magentoOrder = $this->orderRepository->get($check24Return->getOrderId());
                $itemCollection = new OpenTransDataOrderItemCollection();
                foreach ($magentoOrder->getItems() as $item) {
                    $mappingItem = $this
                        ->orderPositionMappingRepository
                        ->findByMagentoPositionId($item->getItemId());
                    $openTransItem = new OpenTransOrderItemEntity(
                        $item->getSku(),
                        -1 * (int)$item->getQtyOrdered(),
                        $item->getPrice(),
                        '',
                        $mappingItem->getCheck24PositionId(),
                        $mappingItem->getOrderUnit()
                    );
                    $itemCollection->add($openTransItem);
                }
                $mappingOrder = $this->orderMappingRepository->findByOrderId($check24Return->getOrderId());

                $openTransCancelResponseEntity = new OpenTransReturnResponseEntity(
                    'return-' . $mappingOrder->getCheck24OrderId(),
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
                $this->sendReturnResponse(
                    $this->apiConfigurationService->findStoreApiConfiguration($storeId),
                    $openTransCancelResponseEntity
                );
                $ordersProcessed++;
                $check24Return->setStatus(1);
                $magentoOrder
                    ->addCommentToStatusHistory(
                        'Return send to CHECK24'
                    );
                $this->orderRepository->save($magentoOrder);
            } catch (Exception $e) {
                $failedOrders++;
                $check24Return
                    ->setErrorMessage($e->getMessage());
            } finally {
                $this->returnRepository->save($check24Return);
            }
        }

        return new ProcessOrderResult($ordersProcessed, $failedOrders);
    }

    private function sendReturnResponse(
        ApiConfiguration                     $apiConfiguration,
        OpenTransDataReturnResponseInterface $document
    )
    {
        $this
            ->returnResponseService
            ->response($apiConfiguration, new OpenTransReturnResponse($document));
    }
}
