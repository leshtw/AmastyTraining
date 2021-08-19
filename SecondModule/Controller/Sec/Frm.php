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

class Frm extends Action implements HttpGetActionInterface, HttpPostActionInterface, CsrfAwareActionInterface
{
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

    public function __construct(
        Context                    $context,
        ScopeConfigInterface       $scopeConfig,
        CheckoutSession            $messageManager,
        ProductRepositoryInterface $productRepository,
        ProductCollectionFactory   $productCollectionFactory
    )
    {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->session = $messageManager;
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
    }



    public function execute()
    {

      $qrt = $this->getRequest()->getPost()->get('qty');
      $sku = $this->getRequest()->getPost()->get('sku');


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
        if ($sku === null) {
            exit;
        }
        try {
            $product = $this->productRepository->get($sku);

        }catch (NoSuchEntityException $e) {
            $this->messageManager->addExceptionMessage($e);
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/index/index');
            return $resultRedirect;

        }

        if ($product->getTypeID() === 'simple') {
            try {
                $quote->addProduct($product, $qrt);
                $quote->save();
                $this->messageManager->addSuccessMessage('added');


            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e);

            }
        } else {
            $this->messageManager->addWarningMessage('not simple');
//            var_dump($product->getTypeID());
//            die();
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
