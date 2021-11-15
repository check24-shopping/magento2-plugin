<?php

namespace Check24Shopping\OrderImport\Service\Api;

use Check24Shopping\OrderImport\Api\DynamicConfigRepositoryInterface;
use Check24Shopping\OrderImport\Helper\Config\ApiConfiguration;
use GuzzleHttp\ClientFactory;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Psr\Http\Message\ResponseInterface;

class ApiService
{
    /** @var ProductMetadataInterface */
    private $productMetadata;
    /** @var string */
    private $phpVersion;
    /** @var ClientFactory */
    private $clientFactory;
    /** @var array */
    private $clients = [];
    /** @var array */
    private $headers;

    public function __construct(
        ObjectManagerInterface $objectManager,
        ClientFactory $clientFactory
    )
    {
        $this->productMetadata = $objectManager->get(ProductMetadataInterface::class);
        $this->phpVersion = phpversion();
        $this->clientFactory = $clientFactory;
        $metaData =
            [
                'ShopSystem' => $this->productMetadata->getName(),
                'ShopSystemVersion' => $this->productMetadata->getVersion(),
                'ShopSystemEdition' => $this->productMetadata->getEdition(),
                'PhpVersion' => $this->phpVersion,
            ];
        $this->headers =
            [
                'X-PluginData' => json_encode($metaData),
            ];
    }

    public function doRequest(
        ApiConfiguration $configuration,
        string $uriEndpoint,
        string $requestMethod = Request::HTTP_METHOD_GET,
        array $parameter = []
    ): ResponseInterface
    {
        return $this
            ->getClient($configuration)
            ->request(
                $requestMethod,
                $uriEndpoint,
                array_merge(
                    [
                        'headers' => $this->headers,
                        'auth' => [
                            $configuration->getUser(),
                            $configuration->getPassword()
                        ]
                    ],
                    $parameter
                )
            );
    }

    private function getClient(ApiConfiguration $configuration)
    {
        if (key_exists($configuration->getId(), $this->clients) === false) {
            $this->clients[$configuration->getId()] = $this
                ->clientFactory
                ->create(
                    [
                        'config' => [
                            'base_uri' => $configuration->getHost() . ':' . $configuration->getPort(),
                        ],
                    ]
                );
        }

        return $this
            ->clients[$configuration->getId()];
    }
}
