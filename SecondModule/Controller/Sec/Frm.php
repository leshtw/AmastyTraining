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

class Frm extends Action implements HttpGetActionInterface, HttpPostActionInterface, CsrfAwareActionInterface
{

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
        ProductCollectionFactory   $productCollectionFactory
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


        $qrt = $this->getRequest()->getPost()->get('qty');
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

//        if (isset($sku)) {
//            $product = $this->productRepository->get($sku);
//            $quote = $this->session->getQuote();
//            if (!$quote->getId()) {
//                $quote->save();
//            }
//            $quote->addProduct($product,$qrt);
//            $quote->save();
//        }

        $qtyMain = $quote->getAllVisibleItems();
        $startQty = 0;

        foreach ($qtyMain as $value => $i) {
            if ($i->getSku() === $sku) {
                $startQrt = $i->getQty();
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
                $quote->addProduct($product, $qrt);
                $quote->save();
                $this->messageManager->addSuccessMessage('added');


            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e);

            }

        } else if ($product->getTypeID() === 'simple' && $blacklistsku->getData('product_qty')) {
            try {
                if ($qrt + $startQty < $blacklistsku->getData('product_qty')) {
                    $quote->addProduct($product, $qrt);
                    $quote->save();
                    $this->messageManager->addSuccessMessage('added');
                } else if ($qrt + $startQty > $blacklistsku->getData('product_qty')) {
                    $quote->addProduct($product, $blacklistsku->getData('product_qty'));
                    $quote->save();
                    $this->messageManager->addSuccessMessage('We were able to add only' . $blacklistsku->getData('product_qty'));
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
