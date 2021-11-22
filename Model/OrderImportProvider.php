<?php

namespace Check24Shopping\OrderImport\Model;

use Check24Shopping\OrderImport\Api\Data\OrderImportInterface;
use Check24Shopping\OrderImport\Api\Data\OrderImportSearchResultsInterface;
use Check24Shopping\OrderImport\Api\OrderImportProviderInterface;
use Check24Shopping\OrderImport\Api\OrderImportRepositoryInterface;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\OpenTransDocumentInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class OrderImportProvider implements OrderImportProviderInterface
{
    /** @var OrderImportRepositoryInterface */
    private $orderRepository;
    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    public function __construct(
        OrderImportRepositoryInterface $orderRepository,
        SearchCriteriaBuilder          $searchCriteriaBuilder
    )
    {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return OrderImportSearchResultsInterface
     */
    public function getImportedList()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(OrderImportInterface::FIELD_STATUS, 0)
            ->addFilter(OrderImportInterface::FIELD_ERROR_MESSAGE, null, 'null')
            ->addFilter(OrderImportInterface::FIELD_TYPE, 'order')
            ->create();

        return $this->orderRepository->getList($searchCriteria);
    }

    /**
     * @return OrderImportSearchResultsInterface
     */
    public function getNotRespondedList()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(OrderImportInterface::FIELD_STATUS, 1)
            ->create();

        return $this->orderRepository->getList($searchCriteria);
    }

    public function getByOrderNumber(string $orderNumber): ?OrderImportInterface
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(OrderImportInterface::FIELD_CHECK24_ORDER_ID, $orderNumber)
            ->create();

        $list = $this->orderRepository->getList($searchCriteria);
        if ($list->getTotalCount() > 0) {
            return current($list->getItems());
        }

        return null;
    }

    public function getCancelList()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                OrderImportInterface::FIELD_ACTION,
                OpenTransDocumentInterface::ACTION_CANCELLATION_REQUEST
            )
            ->addFilter(OrderImportInterface::FIELD_TYPE, 'orderchange')
            ->create();

        return $this->orderRepository->getList($searchCriteria);
    }

    public function getReturnRequestList()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                OrderImportInterface::FIELD_ACTION,
                OpenTransDocumentInterface::ACTION_RETURN_REQUEST
            )
            ->addFilter(OrderImportInterface::FIELD_TYPE, 'orderchange')
            ->create();

        return $this->orderRepository->getList($searchCriteria);
    }
}
