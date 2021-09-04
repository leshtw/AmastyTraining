<?php

namespace Amasty\ThirdModule\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;



class NewController
{

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository
    ){
        $this->productRepository = $productRepository;
    }

    public function beforeExecute(
        $subject
    ){
        $sku = $subject->getRequest()->getParam('sku');
        $productId = $this->productRepository->get($sku)->getId();
        $subject->getRequest()->setParam('product', $productId);
    }





}
