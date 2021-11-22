<?php

namespace Check24Shopping\OrderImport\Model\Task;

use Check24Shopping\OrderImport\Api\Data\OrderImportInterface;
use Check24Shopping\OrderImport\Api\Data\ReturnRequestInterfaceFactory;
use Check24Shopping\OrderImport\Api\DynamicConfigRepositoryInterface;
use Check24Shopping\OrderImport\Api\OrderImportProviderInterface;
use Check24Shopping\OrderImport\Api\OrderImportRepositoryInterface;
use Check24Shopping\OrderImport\Model\Exception\NoOrderMappingFound;
use Check24Shopping\OrderImport\Model\OrderMappingRepository;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\OpenTransDocumentInterface;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\Xml\OpenTransOrderDocument;
use Check24Shopping\OrderImport\Model\ReturnRequest;
use Check24Shopping\OrderImport\Model\ReturnRequestRepository;
use Check24Shopping\OrderImport\Model\Task\Model\ProcessOrderResult;
use Exception;

class ReturnRequestTask
{
    /** @var OrderImportProviderInterface */
    private $orderProvider;
    /** @var OrderImportRepositoryInterface */
    private $orderRepository;
    /** @var OrderMappingRepository */
    private $orderMappingRepository;
    /**
     * @var ReturnRequestRepository
     */
    private $returnRequestRepository;
    /**
     * @var ReturnRequestInterfaceFactory
     */
    private $returnRequestFactory;
    /**
     * @var DynamicConfigRepositoryInterface
     */
    private $dynamicConfigRepository;

    public function __construct(
        OrderImportProviderInterface     $orderProvider,
        OrderImportRepositoryInterface   $orderRepository,
        OrderMappingRepository           $orderMappingRepository,
        ReturnRequestInterfaceFactory    $returnRequestFactory,
        ReturnRequestRepository          $returnRequestRepository,
        DynamicConfigRepositoryInterface $dynamicConfigRepository
    )
    {
        $this->orderMappingRepository = $orderMappingRepository;
        $this->orderProvider = $orderProvider;
        $this->orderRepository = $orderRepository;
        $this->returnRequestRepository = $returnRequestRepository;
        $this->returnRequestFactory = $returnRequestFactory;
        $this->dynamicConfigRepository = $dynamicConfigRepository;
    }

    public function processNotReturnRequest(): ProcessOrderResult
    {
        $returnRequestList = $this->orderProvider->getReturnRequestList();
        if (empty($returnRequestList->getTotalCount())) {
            return new ProcessOrderResult(0, 0);
        }
        $amountProcessed = $failedOrders = 0;
        /** @var OrderImportInterface $returnRequestList */
        foreach ($returnRequestList->getItems() as $returnRequest) {
            try {
                $document = new OpenTransOrderDocument($returnRequest->getContent());
                if ($document->getAction() !== OpenTransDocumentInterface::ACTION_RETURN_REQUEST) {
                    continue;
                }
                if ($this->dynamicConfigRepository->load()->getProcessReturn()) {
                    $this->moveDocument($returnRequest);
                    $amountProcessed++;
                }
            } catch (Exception $e) {
                $failedOrders++;
            } finally {
                $this->orderRepository->delete($returnRequest);
            }
        }

        return new ProcessOrderResult($amountProcessed, $failedOrders);
    }

    private function moveDocument(OrderImportInterface $returnRequest)
    {
        $mappingOrder = $this->orderMappingRepository->findByCheck24OrderId($returnRequest->getCheck24OrderId());
        if (empty($mappingOrder)) {
            throw new NoOrderMappingFound();
        }
        /** @var ReturnRequest $returnRequestEntity */
        $returnRequestEntity = $this
            ->returnRequestFactory
            ->create();
        $returnRequestEntity
            ->setCheck24OrderId($returnRequest->getCheck24OrderId())
            ->setMagentoOrderId($mappingOrder->getMagentoOrderId())
            ->setMagentoOrderIncrementId($mappingOrder->getMagentoOrderIncrementId())
            ->setOrderCreatedAt($mappingOrder->getCreatedAt());
        $this
            ->returnRequestRepository
            ->save($returnRequestEntity);
    }
}
