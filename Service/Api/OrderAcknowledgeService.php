<?php

namespace Check24Shopping\OrderImport\Service\Api;

use Check24Shopping\OrderImport\Helper\Config\ApiConfiguration;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\OpenTransDocumentInterface;
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
