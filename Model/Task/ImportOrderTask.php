<?php

namespace Check24\OrderImport\Model\Task;

use Check24\OrderImport\Api\Data\DocumentTrackingInterfaceFactory;
use Check24\OrderImport\Api\DocumentTrackingRepositoryInterface;
use Check24\OrderImport\Helper\Config\ApiConfiguration;
use Check24\OrderImport\Model\Exception\CanNotParseXml;
use Check24\OrderImport\Model\Exception\Check24OrderNotPersisted;
use Check24\OrderImport\Model\Exception\NoValidConfiguration;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDocumentInterface;
use Check24\OrderImport\Model\Task\Model\ImportOrderResult;
use Check24\OrderImport\Service\Api\ApiConfigurationService;
use Check24\OrderImport\Service\Api\DocumentRequestService;
use Check24\OrderImport\Service\Api\OrderAcknowledgeService;
use Check24\OrderImport\Service\Api\SubmitErrorService;
use Check24\OrderImport\Service\Document\DocumentImportService;
use Exception;
use Psr\Log\LoggerInterface;

class ImportOrderTask
{
    /** @var LoggerInterface */
    private $logger;
    /**
     * @var DocumentRequestService
     */
    private $documentRequestService;
    /**
     * @var OrderAcknowledgeService
     */
    private $acknowledgeService;
    /**
     * @var ApiConfigurationService
     */
    private $apiConfigurationService;
    /**
     * @var DocumentTrackingRepositoryInterface
     */
    private $documentTrackingRepository;
    /**
     * @var DocumentTrackingInterfaceFactory
     */
    private $documentTrackingFactory;
    /**
     * @var DocumentImportService
     */
    private $documentImportService;
    /**
     * @var SubmitErrorService
     */
    private $submitErrorService;

    public function __construct(
        LoggerInterface                     $logger,
        DocumentRequestService              $documentRequestService,
        OrderAcknowledgeService             $acknowledgeService,
        ApiConfigurationService             $apiConfigurationService,
        DocumentTrackingRepositoryInterface $documentTrackingRepository,
        DocumentTrackingInterfaceFactory    $documentTrackingFactory,
        DocumentImportService               $documentImportService,
        SubmitErrorService                  $submitErrorService
    )
    {
        $this->logger = $logger;
        $this->documentRequestService = $documentRequestService;
        $this->acknowledgeService = $acknowledgeService;
        $this->apiConfigurationService = $apiConfigurationService;
        $this->documentTrackingRepository = $documentTrackingRepository;
        $this->documentTrackingFactory = $documentTrackingFactory;
        $this->documentImportService = $documentImportService;
        $this->submitErrorService = $submitErrorService;
    }

    /**
     * @throws NoValidConfiguration
     * @throws Check24OrderNotPersisted
     * @throws Exception
     */
    public function execute(): ImportOrderResult
    {
        $apiConfigurations = $this->apiConfigurationService->findAllApiConfigurations();
        if (empty($apiConfigurations)) {
            throw new NoValidConfiguration();
        }
        $amountImportedOrders = $amountSkippedOrders = 0;
        foreach ($apiConfigurations as $apiConfiguration) {
            $importOrderResult = $this->importDocuments($apiConfiguration);
            $amountImportedOrders += $importOrderResult->getAmountImportedOrders();
            $amountSkippedOrders = $importOrderResult->getAmountSkippedOrders();
        }

        return new ImportOrderResult($amountImportedOrders, $amountSkippedOrders);
    }

    /**
     * @throws Check24OrderNotPersisted
     * @throws CanNotParseXml
     * @throws Exception
     */
    private function importDocuments(ApiConfiguration $apiConfiguration): ImportOrderResult
    {
        $loopLimiter = $amountImportedOrders = $amountSkippedOrders = 0;
        try {
            $this->logger->info(
                'trying to connect to ' . $apiConfiguration->getUser() . '@' . $apiConfiguration->getHost()
            );
            while (($orderDocument = $this->documentRequestService->request($apiConfiguration)) &&
                $loopLimiter++ < 100
            ) {
                $documentTracking = $this
                    ->documentTrackingRepository
                    ->findByDocumentId($orderDocument->getDocumentId());
                if (empty($documentTracking) === false) {
                    $this->logger->debug('document already imported');
                    $this->acknowledgeDocument($apiConfiguration, $orderDocument);
                    $amountSkippedOrders++;
                    continue;
                }
                if ($this->documentImportService->documentShouldBeImported($orderDocument) === false) {
                    $this->acknowledgeDocument($apiConfiguration, $orderDocument);
                    $amountSkippedOrders++;
                    continue;
                }
                $this->documentImportService->saveDocument($orderDocument);
                $amountImportedOrders++;
                $this
                    ->documentTrackingRepository
                    ->save(
                        $this
                            ->documentTrackingFactory
                            ->create()
                            ->setDocumentId($orderDocument->getDocumentId())
                    );
                $this->acknowledgeDocument($apiConfiguration, $orderDocument);
            }
        } catch (CanNotParseXml $exception) {
            $this
                ->submitErrorService
                ->request($apiConfiguration, $exception);
            throw $exception;
        } catch (Check24OrderNotPersisted $exception) {
            throw $exception;
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw $exception;
        }

        return new ImportOrderResult($amountImportedOrders, $amountSkippedOrders);
    }

    private function acknowledgeDocument(
        ApiConfiguration           $apiConfiguration,
        OpenTransDocumentInterface $openTransDocument
    )
    {
        $this
            ->acknowledgeService
            ->acknowledge($apiConfiguration, $openTransDocument);
        $this->logger->debug('ok -> document acknowledged');
    }
}
