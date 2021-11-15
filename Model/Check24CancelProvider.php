<?php

namespace Check24\OrderImport\Model;

use Check24\OrderImport\Api\Check24CancelProviderInterface;
use Check24\OrderImport\Api\Check24CancelRepositoryInterface;
use Check24\OrderImport\Api\Data\Check24CancelInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class Check24CancelProvider implements Check24CancelProviderInterface
{
    /** @var Check24CancelRepositoryInterface */
    private $cancelRepository;
    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    public function __construct(
        Check24CancelRepositoryInterface $cancelRepository,
        SearchCriteriaBuilder            $searchCriteriaBuilder
    )
    {
        $this->cancelRepository = $cancelRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function getNotSubmitted()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(Check24CancelInterface::FIELD_STATUS, 0)
            ->create();

        return $this->cancelRepository->getList($searchCriteria);
    }
}
