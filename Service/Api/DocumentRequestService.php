<?php

namespace Check24Shopping\OrderImport\Service\Api;

use Check24Shopping\OrderImport\Api\DynamicConfigRepositoryInterface;
use Check24Shopping\OrderImport\Helper\Config\ApiConfiguration;
use Check24Shopping\OrderImport\Model\Exception\CanNotParseXml;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\OpenTransDocumentInterface;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\Xml\OpenTransDocument;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class DocumentRequestService
{
    const API_ENDPOINT = '/api/shop/document';

    /** @var ApiService */
    private $apiService;
    /** @var DynamicConfigRepositoryInterface */
    private $dynamicConfigRepository;

    public function __construct(
        ApiService $apiService,
        DynamicConfigRepositoryInterface $dynamicConfigRepository
    )
    {
        $this->apiService = $apiService;
        $this->dynamicConfigRepository = $dynamicConfigRepository;
    }

    /**
     * @throws CanNotParseXml
     */
    public function request(ApiConfiguration $apiConfiguration): ?OpenTransDocumentInterface
    {
        $response = $this
            ->apiService
            ->doRequest(
                $apiConfiguration,
                self::API_ENDPOINT
            );
        $content = $response->getBody()->getContents();
        $this
            ->setConfiguration($response);
        if (empty($content)) {
            return null;
        }
        try {
            return new OpenTransDocument($content);
        } catch (Throwable $exception) {

            throw new CanNotParseXml(
                (string)$response->getHeader('x-ordernumber')[0] ?? '',
                (string)$response->getHeader('x-documentnumber')[0] ?? '',
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    private function setConfiguration(ResponseInterface $response)
    {
        $responseHeaders = $response->getHeaders();
        foreach ($responseHeaders as $key => $value){
            $responseHeaders[strtolower($key)] = $value;
        }
        $fieldMapping = [
            'x-confirmshipment' => 'SendDispatch',
            'x-receivecancellation' => 'ProcessCancel',
            'x-sendcancellation' => 'SendCancel',
            'x-receivereturn' => 'ProcessReturn',
            'x-sendreturn' => 'SendReturn',
        ];
        $dynamicConfig = $this->dynamicConfigRepository->load();
        $isChanged = false;
        foreach ($fieldMapping as $headerKey => $functionName) {
            if (key_exists($headerKey, $responseHeaders)) {
                $value = (bool)reset($responseHeaders[$headerKey]);
                if ($dynamicConfig->{'get' . $functionName}() !== $value) {
                    $dynamicConfig->{'set' . $functionName}($value);
                    $isChanged = true;
                }
            }
        }
        if ($isChanged) {
            $this
                ->dynamicConfigRepository
                ->save(
                    $dynamicConfig
                );
        }
    }
}
