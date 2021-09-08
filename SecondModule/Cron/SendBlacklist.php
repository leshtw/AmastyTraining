<?php

namespace Amasty\SecondModule\Cron;


use Magento\Framework\Controller\ResultFactory;
use Amasty\SecondModule\Model\Blacklist;
use Amasty\SecondModule\Model\BlacklistFactory;
use Amasty\SecondModule\Model\ResourceModel\Blacklist as BlacklistResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

class SendBlacklist
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
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

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        BlacklistFactory $blacklistFactory,
        BlacklistResource $blacklistResource,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder
    ){
        $this->scopeConfig = $scopeConfig;
        $this->blacklistFactory = $blacklistFactory;
        $this->blacklistResource = $blacklistResource;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
    }
    public function execute()
    {

        /**
         *  @var Amasty\SecondModule\Model\Blacklist $blacklist
         */
        $blacklist = $this->blacklistFactory->create();

        $this->blacklistResource->load(
            $blacklist,
            1, //id in blacklist_id
            'blacklist_id'
        );

        $temlateId = 'my_config_general_email_templates';
        $senderName = 'Alex';
        $senderEmail = $this->scopeConfig->getValue('my_config/general/email_user');
        $toEmail = 'alex@gmail.com';
        $tamplateVars = [
            'blacklist' => $blacklist,
            'sku' => $blacklist->getData('product_sku'),
            'qty' => $blacklist->getData('product_qty')
        ];
        $storeId = $this->storeManager->getStore()->getId();
        $from = [
            'email' => $senderEmail,
            'name' => $senderName
        ];
        /**@var  \Magento\Email\Model\Transport $transport */
        $transport = $this->transportBuilder->setTemplateIdentifier($temlateId, ScopeInterface::SCOPE_STORE)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId
                ]
            )->setTemplateVars($tamplateVars)
            ->setFromByScope($from)
            ->addTo($toEmail)
            ->getTransport();
        $message = $transport->getMessage();
        $messageText = $message->getBodyText();

        $blacklist->setEmail($messageText); // заносим в столбец с неймом Email
        $blacklist->save();

    }
}
