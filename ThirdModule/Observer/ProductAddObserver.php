<?php

namespace Amasty\ThirdModule\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;

class ProductAddObserver implements ObserverInterface
{


    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var CheckoutSession
     */
    private $session;


    public function __construct
    (
        CheckoutSession   $checkoutSession,
        ProductRepository $productRepository,
        ScopeConfig       $scopeConfig
    )
    {
        $this->session = $checkoutSession;
        $this->productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
    }


    public function execute(Observer $observer)
    {

        $sku = $observer->getData('check_sku');
        $promoSku = $this->scopeConfig->getValue('thirdmodule_config/general/promo_sku');
        $forSku = explode(',', $this->scopeConfig->getValue('thirdmodule_config/general/for_sku'));

        if ($promoSku) {
            $prmProduct = $this->productRepository->get($promoSku);
            foreach ($forSku as $itemForSky) {
                if ($itemForSky == $sku) {
                    $quote = $this->session->getQuote();
                    $quote->addProduct($prmProduct, 1);
                    $quote->save();

                }
            }
        }

    }

}
