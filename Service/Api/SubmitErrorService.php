<?php

namespace Check24Shopping\OrderImport\Service\Api;

use Check24Shopping\OrderImport\Helper\Config\ApiConfiguration;
use Check24Shopping\OrderImport\Model\ValueObject\Interfaces\ErrorMessageInterface;
use Magento\Framework\Webapi\Rest\Request;
use Throwable;

class SubmitErrorService
{
    const API_ENDPOINT = '/api/shop/document/{documentNumber}/error';

    /** @var ApiService */
    private $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function request(
        ApiConfiguration      $apiConfiguration,
        ErrorMessageInterface $errorMessage
    ): void
    {
        try {
            $this
                ->apiService
                ->doRequest(
                    $apiConfiguration,
                    str_replace('{documentNumber}', $errorMessage->getDocumentNumber(), self::API_ENDPOINT),
                    Request::METHOD_POST,
                    [
                        'body' => json_encode(
                            [
                                'message' => $errorMessage->getErrorMessage(),
                                'orderNumber' => $errorMessage->getOrderNumber(),
                            ]
                        ),
                    ]
                );
        } catch (Throwable $exception) {
            unset($exception);
        } finally {
            return;
        }
    }
}
