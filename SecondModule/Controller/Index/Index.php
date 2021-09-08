<?php

namespace Amasty\SecondModule\Controller\Index;


use Amasty\SecondModule\Model\Blacklist;
use Amasty\SecondModule\Model\BlacklistFactory;
use Amasty\SecondModule\Model\ResourceModel\Blacklist as BlacklistResource;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

class Index extends Action implements HttpGetActionInterface
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
     * @var StoreManagerInterface $storeManager
     */
    protected $storeManager;
    /**
     * @var TransportBuilder $transportBuilder
     */
    protected $transportBuilder;
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
        ProductRepositoryInterface $productRepository,
 BlacklistFactory $blacklistFactory,
        BlacklistResource $blacklistResource,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder
    )
    {
        $this->blacklistFactory = $blacklistFactory;
        $this->blacklistResource = $blacklistResource;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->request = $request;
        $this->session = $session;
        $this->productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function execute()
    {

//        /**
//         *  @var Amasty\SecondModule\Model\Blacklist $blacklist
//         */
//        $blacklist = $this->blacklistFactory->create();
//
//        $this->blacklistResource->load(
//            $blacklist,
//            1,
//            'blacklist_id'
//        );
//
//        $temlateId = 'my_config_general_email_templates';
//        $senderName = 'Alex';
//        $senderEmail = $this->scopeConfig->getValue('my_config/general/email_user');
//        $toEmail = 'alex@gmail.com';
//        $tamplateVars = [
//            'blacklist' => $blacklist,
//            'sku' => $blacklist->getData('product_sku'),
//            'qty' => $blacklist->getData('product_qty'),
//        ];
//        $storeId = $this->storeManager->getStore()->getId();
//        $from = [
//            'email' => $senderEmail,
//            'name' => $senderName
//        ];
//        /**@var  \Magento\Email\Model\Transport $transport  */
//        $transport = $this->transportBuilder->setTemplateIdentifier($temlateId, ScopeInterface::SCOPE_STORE)
//            ->setTemplateOptions(
//                [
//                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
//                    'store' => $storeId
//                ]
//            )->setTemplateVars($tamplateVars)
//            ->setFromByScope($from)
//            ->addTo($toEmail)
//            ->getTransport();
//        $message = $transport->getMessage();
//        $messageText = $message->getBodyText();
//
//        $blacklist->setEmail($messageText);
//        $blacklist->save();


        if ($this->scopeConfig->isSetFlag('my_config/general/enabled')) {
            return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        } else {
            die('Sorry, module off');
        }
    }
}
