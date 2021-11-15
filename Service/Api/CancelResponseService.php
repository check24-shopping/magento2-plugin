<?php

namespace Check24\OrderImport\Service\Api;

use Check24\OrderImport\Helper\Config\ApiConfiguration;
use Check24\OrderImport\Model\Writer\OpenTrans\OpenTransCancelResponse;
use Magento\Framework\Webapi\Rest\Request;

class CancelResponseService
{
    const API_ENDPOINT = '/api/shop/document';

    /** @var ApiService */
    private $apiService;

    public function __construct(
        ApiService $apiService
    )
    {
        $this->apiService = $apiService;
    }

    public function response(
        ApiConfiguration        $apiConfiguration,
        OpenTransCancelResponse $openTransCancelResponse
    ): void
    {
        $this
            ->apiService
            ->doRequest(
                $apiConfiguration,
                self::API_ENDPOINT,
                Request::METHOD_POST,
                [
                    'body' => $openTransCancelResponse->getXmlString(),
                ]
            );
    }
}
