<?php

namespace Check24Shopping\OrderImport\Controller\Adminhtml\ReturnRequest;

use Check24Shopping\OrderImport\Model\ResourceModel\ReturnRequest\Collection;
use Check24Shopping\OrderImport\Model\ResourceModel\ReturnRequest\CollectionFactory;
use Check24Shopping\OrderImport\Model\ReturnRequestRepository;
use Magento\Ui\Component\MassAction\Filter;
use Exception;
use Magento\Backend\App\Action;

class DeleteReturnRequest extends Action
{
    /**
     * @var ReturnRequestRepository
     */
    private $returnRequestRepository;
    /**
     * @var Filter
     */
    private $filter;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Action\Context          $context,
        ReturnRequestRepository $returnRequestRepository,
        Filter $filter,
        CollectionFactory $collectionFactory
    )
    {
        parent::__construct($context);
        $this->returnRequestRepository = $returnRequestRepository;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            foreach ($collection->getAllIds() as $id) {
                $returnRequest = $this->returnRequestRepository->load($id);
                if ($returnRequest->getId() > 0) {
                    $this
                        ->returnRequestRepository
                        ->delete($returnRequest);
                }
            }
            $this->messageManager->addSuccessMessage(__('Success'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->_redirect('*/*/index');
    }
}
