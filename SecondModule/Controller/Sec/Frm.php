<?php

namespace Amasty\SecondModule\Controller\Sec;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Amasty\SecondModule\Model\Blacklist;
use Amasty\SecondModule\Model\BlacklistFactory;
use Amasty\SecondModule\Model\ResourceModel\Blacklist as BlacklistResource;
use Amasty\SecondModule\Model\ResourceModel\Blacklist\CollectionFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;


class Frm extends Action implements HttpGetActionInterface, HttpPostActionInterface, CsrfAwareActionInterface
{

    /**
     * @var EventManager
     */
    private $eventManager;
    /**
     * @var BlacklistFactory
     */
    protected $blacklistFactory;
    /**
     * @var BlacklistResource
     */
    protected $blacklistResource;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var CheckoutSession
     */
    private $session;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;


    public function __construct(
        CollectionFactory          $collectionFactory,
        BlacklistFactory           $blacklistFactory,
        BlacklistResource          $blacklistResource,
        Context                    $context,
        ScopeConfigInterface       $scopeConfig,
        CheckoutSession            $messageManager,
        ProductRepositoryInterface $productRepository,
        ProductCollectionFactory   $productCollectionFactory,
        EventManager               $eventManager
    )
    {
        parent::__construct($context);
        $this->blacklistFactory = $blacklistFactory;
        $this->blacklistResource = $blacklistResource;
        $this->scopeConfig = $scopeConfig;
        $this->session = $messageManager;
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->collectionFactory = $collectionFactory;
        $this->eventManager = $eventManager;
    }


    public function execute()
    {

//        /**
//         * @var Amasty\SecondModule\Model\Blacklist $blacklist
//         */
//
//        $blacklist = $this->blacklistFactory->create();
//
//
//        $blacklist->setText('bla bla');
//        $this->blacklistResource->save($blacklist);


        $qty = $this->getRequest()->getPost()->get('qty');
        $sku = $this->getRequest()->getPost()->get('sku');


//
//        $blacklistCollection = $this->collectionFactory->create();
//        $blacklistCollection->addFieldToFilter(
//            'product_sku',
//            ['eq' => $sku]
//        );
//        foreach($blacklistCollection as $item){
//
//        }


        $quote = $this->session->getQuote();
        if (!$quote->getId()) {
            $quote->save();
        }
        $product = false;

        $this->eventManager->dispatch(
            'amasty_secondmodule_product_add',
            ['check_sku' => $sku]
        );

//        if (isset($sku)) {
//            $product = $this->productRepository->get($sku);
//            $quote = $this->session->getQuote();
//            if (!$quote->getId()) {
//                $quote->save();
//            }
//            $quote->addProduct($product,$qty);
//            $quote->save();
//        }

        $qtyMain = $quote->getAllVisibleItems();
        $startQty = 0;

        foreach ($qtyMain as $value => $i) {
            if ($i->getSku() === $sku) {
                $startQty = $i->getQty();
            }
        }


        if ($sku === null) {
            exit;
        }
        try {
            $product = $this->productRepository->get($sku);

        } catch (NoSuchEntityException $e) {
            $this->messageManager->addExceptionMessage($e);
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/index/index');
            return $resultRedirect;
        }

        /**
         * @var Amasty\SecondModule\Model\Blacklist $blacklistsku
         */
        $blacklistsku = $this->blacklistFactory->create();
        $this->blacklistResource->load(
            $blacklistsku,
            $sku,
            'product_sku'

        );


        if ($product->getTypeID() === 'simple' && !$blacklistsku->getData('product_qty')) {


            try {
                $quote->addProduct($product, $qty);
                $quote->save();
                $this->messageManager->addSuccessMessage('added');


            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e);

            }

        } else if ($product->getTypeID() === 'simple' && $blacklistsku->getData('product_qty')) {
            try {
                if ($qty + $startQty < $blacklistsku->getData('product_qty')) {
                    $quote->addProduct($product, $qty);
                    $quote->save();
                    $this->messageManager->addSuccessMessage('added');
                } else if ($qty + $startQty > $blacklistsku->getData('product_qty')) {
                    $quote->addProduct($product, $blacklistsku->getData('product_qty'));
                    $quote->save();
                    $this->messageManager->addSuccessMessage('We were able to add only' ." ". $blacklistsku->getData('product_qty'));
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e);
            }
        } else if (!($product->getTypeID() === 'simple')) {
            $this->messageManager->addWarningMessage('not simple');

        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/index/index');
        return $resultRedirect;


    }


    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
