<?php

namespace Check24\OrderImport\Service\Api;

use Check24\OrderImport\Helper\Config\ApiConfiguration;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDocumentInterface;
use Magento\Framework\Webapi\Rest\Request;

class OrderAcknowledgeService
{
    const API_ENDPOINT = '/api/shop/document/{document}/acknowledge';

    /** @var ApiService */
    private $apiService;

    public function __construct(
        ApiService $apiService
    )
    {
        $this->apiService = $apiService;
    }

    public function acknowledge(
        ApiConfiguration           $apiConfiguration,
        OpenTransDocumentInterface $openTransDocument
    ): void
    {
        $this
            ->apiService
            ->doRequest(
                $apiConfiguration,
                str_replace('{document}', $openTransDocument->getDocumentId(), self::API_ENDPOINT),
                Request::HTTP_METHOD_PUT
            );
    }
}
