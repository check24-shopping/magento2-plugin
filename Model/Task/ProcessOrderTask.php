<?php

namespace Check24Shopping\OrderImport\Model\Task;

use Check24Shopping\OrderImport\Api\OrderImportProviderInterface;
use Check24Shopping\OrderImport\Api\OrderImportRepositoryInterface;
use Check24Shopping\OrderImport\Api\OrderManagementInterface;
use Check24Shopping\OrderImport\Helper\Config\OrderConfig;
use Check24Shopping\OrderImport\Model\Exception\MissingMatchingStore;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\OpenTransDataAddressInterface;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\OpenTransDataPartyInterface;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\Xml\OpenTransOrderDocument;
use Check24Shopping\OrderImport\Model\Task\Model\ProcessOrderResult;
use Check24Shopping\OrderImport\Service\Mapping\NewMappingService;
use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class ProcessOrderTask
{
    /** @var AppEmulation */
    private $appEmulation;
    /** @var OrderImportProviderInterface */
    private $orderProvider;
    /** @var OrderManagementInterface */
    private $orderManagement;
    /** @var StoreManagerInterface */
    private $storeManager;
    /** @var CartRepositoryInterface */
    private $cartRepository;
    /** @var CartManagementInterface */
    private $cartManagement;
    /** @var ProductRepositoryInterface */
    private $productRepository;
    /** @var OrderConfig */
    private $orderConfig;
    /** @var QuoteRepository */
    private $quoteRepository;
    /** @var MagentoOrderRepositoryInterface */
    private $magentoOrderRepository;
    /** @var OrderImportRepositoryInterface */
    private $orderRepository;
    /** @var NewMappingService */
    private $newMappingService;

    public function __construct(
        AppEmulation                    $appEmulation,
        OrderImportProviderInterface    $orderProvider,
        OrderManagementInterface        $orderManagement,
        OrderImportRepositoryInterface  $orderRepository,
        StoreManagerInterface           $storeManager,
        CartRepositoryInterface         $cartRepository,
        CartManagementInterface         $cartManagement,
        ProductRepositoryInterface      $productRepository,
        OrderConfig                     $orderConfig,
        QuoteRepository                 $quoteRepository,
        MagentoOrderRepositoryInterface $magentoOrderRepository,
        NewMappingService               $newMappingService
    )
    {
        $this->appEmulation = $appEmulation;
        $this->orderProvider = $orderProvider;
        $this->orderManagement = $orderManagement;
        $this->storeManager = $storeManager;
        $this->cartRepository = $cartRepository;
        $this->cartManagement = $cartManagement;
        $this->productRepository = $productRepository;
        $this->orderConfig = $orderConfig;
        $this->quoteRepository = $quoteRepository;
        $this->magentoOrderRepository = $magentoOrderRepository;
        $this->orderRepository = $orderRepository;
        $this->newMappingService = $newMappingService;
    }

    public function processNotProcessedOrders(): ProcessOrderResult
    {
        $orderList = $this->orderProvider->getImportedList();
        if (empty($orderList->getTotalCount())) {
            return new ProcessOrderResult(0, 0);
        }
        $ordersProcessed = $failedOrders = 0;
        foreach ($orderList->getItems() as $order) {
            try {
                $orderDocument = new OpenTransOrderDocument($order->getContent());
                $magentoOrder = $this->processOrderDocument($orderDocument);
                $order
                    ->setStatus(true)
                    ->setMagentoOrderId($magentoOrder->getId())
                    ->setMagentoOrderIncrementId($magentoOrder->getIncrementId());
                $this
                    ->newMappingService
                    ->save($magentoOrder, $orderDocument);
                $ordersProcessed++;
            } catch (Exception $e) {
                $failedOrders++;
                $order
                    ->setErrorMessage($e->getMessage())
                    ->setErrorDetails(
                        $e->getFile() . ':(' . $e->getLine() . ")\n" . $e->getTraceAsString()
                    );
            } finally {
                $this->orderRepository->save($order);
            }
        }

        return new ProcessOrderResult($ordersProcessed, $failedOrders);
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws MissingMatchingStore
     * @throws NoSuchEntityException
     */
    private function processOrderDocument(OpenTransOrderDocument $orderDocument): OrderInterface
    {
        $partnerId = $orderDocument->getPartnerId();
        $storeId = $this->orderManagement->getStoreIdByPartnerId($partnerId);
        if (empty($storeId)) {
            throw new MissingMatchingStore('No matching store id for partner id "' . $partnerId . '"');
        }

        $this->appEmulation->startEnvironmentEmulation($storeId);

        $magentoOrder = $this->createMagentoOrder($orderDocument, $storeId);
        // Update shipping cost & total (simple update - no tax calculation)
        $shippingAmount = $orderDocument->getShippingAmount();
        if ($shippingAmount) {
            $configuredShippingAmount = $magentoOrder->getShippingAmount();
            $magentoOrder
                ->setShippingAmount($shippingAmount)
                ->setBaseShippingAmount($shippingAmount)
                ->setShippingInclTax($shippingAmount)
                ->setBaseShippingInclTax($shippingAmount)
                ->setGrandTotal($magentoOrder->getGrandTotal() - $configuredShippingAmount + $shippingAmount)
                ->save();
        }
        $this->appEmulation->stopEnvironmentEmulation();

        return $magentoOrder;
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function createMagentoOrder(OpenTransOrderDocument $orderDocument, int $storeId): OrderInterface
    {
        /** @var Store $store */
        $store = $this->storeManager->getStore($storeId);
        $cartId = $this->cartManagement->createEmptyCart();

        /** @var Quote $quote */
        $quote = $this->cartRepository->get($cartId);

        $this->setBaseData($quote, $orderDocument, $store);
        $this->setBillingAddress($quote, $orderDocument->getInvoiceParty());
        $this->setShippingAddress($quote, $orderDocument->getDeliveryParty());
        $this->setOrderItems($quote, $orderDocument, $storeId);
        $this->setShippingMethod($quote);
        $this->setPaymentMethod($quote);
        $quote->collectTotals();

        $this->quoteRepository->save($quote);

        $orderId = $this->cartManagement->placeOrder($quote->getId());

        return $this->magentoOrderRepository->get($orderId);
    }

    private function setBaseData(Quote $quote, OpenTransOrderDocument $orderDocument, StoreInterface $store)
    {
        $deliveryPartyAddress = $orderDocument->getDeliveryParty()->getAddress();
        $quote
            ->setStore($store)
            ->setCurrency()
            ->setCustomerNote($orderDocument->getOrderId())
            ->setCustomerIsGuest(true)
            ->setCustomerEmail($deliveryPartyAddress->getEmail())
            ->setCustomerFirstname($deliveryPartyAddress->getFirstname())
            ->setCustomerLastname($deliveryPartyAddress->getLastname())
            ->setInventoryProcessed(false);
    }

    private function setBillingAddress(Quote $quote, OpenTransDataPartyInterface $invoiceParty)
    {
        $this->setAddress(
            $invoiceParty->getAddress(),
            $quote->getBillingAddress(),
            $quote->getStoreId()
        );
    }

    private function setAddress(OpenTransDataAddressInterface $address, Address $quoteAddress, int $storeId): void
    {
        $quoteAddress
            ->setCompany($address->getCompany())
            ->setFirstname($address->getFirstname())
            ->setLastname($address->getLastname())
            ->setStreet(
                $this->orderManagement->buildStreetData(
                    $address->getStreet(),
                    $address->getRemarks(),
                    $storeId
                )
            )
            ->setPostcode($address->getZip())
            ->setCity($address->getCity())
            ->setCountryId($address->getCountryCode() ?: 'DE')
            ->setRegionId('')
            ->setTelephone($address->getPhone());
    }

    private function setShippingAddress(Quote $quote, OpenTransDataPartyInterface $deliveryParty)
    {
        $this->setAddress(
            $deliveryParty->getAddress(),
            $quote->getShippingAddress(),
            $quote->getStoreId()
        );
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    private function setOrderItems(Quote $quote, OpenTransOrderDocument $orderDocument, int $storeId)
    {
        foreach ($orderDocument->getOrderItems() as $orderItem) {
            // Load product by sku/id depending on configuration
            $productId = $this->orderConfig->getImportAttributeId($storeId);
            if ($productId == 'id') {
                $product = $this->productRepository->getById($orderItem->getSku(), false, $quote->getStoreId());
            } else {
                $product = $this->productRepository->get($orderItem->getSku(), false, $quote->getStoreId());
            }

            $product->setName($orderItem->getDescriptionShort());

            $quoteItem = $quote->addProduct($product, $orderItem->getQuantity());

            $quoteItem
                ->setAdditionalData($orderItem->getId())
                ->setCustomPrice($orderItem->getPrice())
                ->setOriginalCustomPrice($orderItem->getPrice())
                ->getProduct()
                ->setIsSuperMode(true);
        }
    }

    private function setShippingMethod(Quote $quote)
    {
        $quote->getShippingAddress()
            ->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod($this->orderConfig->getShippingCarrier($quote->getStoreId()));
    }

    private function setPaymentMethod(Quote $quote)
    {
        $quote->setPaymentMethod('check24');
        $quote->getPayment()->importData(['method' => 'check24']);
    }
}
