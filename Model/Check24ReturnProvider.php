<?php

namespace Check24Shopping\OrderImport\Model;

use Check24Shopping\OrderImport\Api\Check24ReturnProviderInterface;
use Check24Shopping\OrderImport\Api\Check24ReturnRepositoryInterface;
use Check24Shopping\OrderImport\Api\Data\Check24ReturnInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class Check24ReturnProvider implements Check24ReturnProviderInterface
{
    /** @var Check24ReturnRepositoryInterface */
    private $returnRepository;
    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    public function __construct(
        Check24ReturnRepositoryInterface $returnRepository,
        SearchCriteriaBuilder            $searchCriteriaBuilder
    )
    {
        $this->returnRepository = $returnRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function getNotSubmitted()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(Check24ReturnInterface::FIELD_STATUS, 0)
            ->create();

        return $this->returnRepository->getList($searchCriteria);
    }
}
