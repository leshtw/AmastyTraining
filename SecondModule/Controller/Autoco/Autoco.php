<?php

namespace Amasty\SecondModule\Controller\Autoco;

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
use Magento\Framework\Controller\Result\JsonFactory;


class Autoco extends Action implements HttpGetActionInterface, HttpPostActionInterface, CsrfAwareActionInterface
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
        ProductCollectionFactory   $productCollectionFactory,
        JsonFactory                $jsonResultFactory
    )
    {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->session = $messageManager;
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->jsonResultFactory = $jsonResultFactory;
    }



    public function execute(){

        $res = [];
        $sku = $this->getRequest()->getParam('item');

        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect(["name"])->addAttributeToFilter("sku", ["like" => $sku . "%"])->setPageSize(10);




        foreach ($productCollection as $product) {
            $res[] = "sku: ".$product->getSku()." | name: " .$product->getName();
        }

        $result = $this->jsonResultFactory->create();
        $result->setData($res);
        return $result;


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
