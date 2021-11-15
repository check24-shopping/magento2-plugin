<?php

namespace Check24\OrderImport\Model;

use Check24\OrderImport\Api\Check24ShipmentProviderInterface;
use Check24\OrderImport\Api\Check24ShipmentRepositoryInterface;
use Check24\OrderImport\Api\Data\Check24ShipmentInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class Check24ShipmentProvider implements Check24ShipmentProviderInterface
{
    /** @var Check24ShipmentRepositoryInterface */
    private $orderRepository;
    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    public function __construct(
        Check24ShipmentRepositoryInterface $orderRepository,
        SearchCriteriaBuilder              $searchCriteriaBuilder
    )
    {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function getNotSubmitted()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(Check24ShipmentInterface::FIELD_STATUS, 0)
            ->create();

        return $this->orderRepository->getList($searchCriteria);
    }
}
