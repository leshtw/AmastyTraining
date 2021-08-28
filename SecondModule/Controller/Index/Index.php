<?php

namespace Amasty\SecondModule\Controller\Index;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class Index extends Action implements HttpGetActionInterface
{



    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;


    /**
     * @var CheckoutSession;
     */
    private $session;

    /**
     * @var ProductRepositoryInterface ;
     */
    private $productRepository;

    /**
     * Request instance
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(

        Context                    $context,
        RequestInterface           $request,
        ScopeConfigInterface       $scopeConfig,
        CheckoutSession            $session,
        ProductRepositoryInterface $productRepository

    )
    {
        $this->request = $request;
        $this->session = $session;
        $this->productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function execute()
    {



        if ($this->scopeConfig->isSetFlag('my_config/general/enabled')) {
            return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        } else {
            die('Sorry, module off');
        }
    }
}
