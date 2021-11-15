<?php

namespace Check24Shopping\OrderImport\Service\Document;

use Check24Shopping\OrderImport\Api\Data\DynamicConfigInterface;
use Check24Shopping\OrderImport\Api\Data\OrderImportInterface;
use Check24Shopping\OrderImport\Api\Data\OrderImportInterfaceFactory;
use Check24Shopping\OrderImport\Api\OrderImportRepositoryInterface;
use Check24Shopping\OrderImport\Model\DynamicConfigRepository;
use Check24Shopping\OrderImport\Model\Exception\Check24OrderNotPersisted;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\OpenTransDocumentInterface;
use Psr\Log\LoggerInterface;

class DocumentImportService
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var OrderImportInterfaceFactory
     */
    private $orderImportFactory;
    /**
     * @var OrderImportRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var DynamicConfigInterface
     */
    private $dynamicConfig;

    public function __construct(
        LoggerInterface                $logger,
        OrderImportInterfaceFactory    $orderFactory,
        OrderImportRepositoryInterface $orderRepository,
        DynamicConfigRepository        $dynamicConfigRepository
    )
    {
        $this->logger = $logger;
        $this->orderImportFactory = $orderFactory;
        $this->orderRepository = $orderRepository;
        $this->dynamicConfig = $dynamicConfigRepository->load();
    }

    /**
     * @throws Check24OrderNotPersisted
     */
    public function saveDocument(OpenTransDocumentInterface $openTransDocument): OrderImportInterface
    {
        $this->logger->debug('creating document import');

        $order = $this->orderRepository->save(
            $this
                ->orderImportFactory
                ->create()
                ->setCheck24OrderId($openTransDocument->getOrderId())
                ->setContent($openTransDocument->getContent())
                ->setType($openTransDocument->getType())
                ->setAction($openTransDocument->getAction())
        );

        $this->logger->debug('checking if document entity has a valid id');
        if (((int)$order->getId() > 0) === false) {
            $this->logger->error('error -> no valid id');
            throw new Check24OrderNotPersisted();
        }

        return $order;
    }

    public function documentShouldBeImported(OpenTransDocumentInterface $openTransDocument): bool
    {
        $action = $openTransDocument->getAction();
        if (
            $openTransDocument->getType() === 'order' ||
            ($action === OpenTransDocumentInterface::ACTION_CANCELLATION_REQUEST && $this->dynamicConfig->getProcessCancel()) ||
            ($action === OpenTransDocumentInterface::ACTION_RETURN_REQUEST && $this->dynamicConfig->getProcessReturn())
        ) {
            return true;
        }

        return false;
    }
}
