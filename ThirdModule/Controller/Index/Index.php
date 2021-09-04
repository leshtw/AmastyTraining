<?php
 namespace Amasty\ThirdModule\Controller\Index;

 use Magento\Customer\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;



class Index extends Action implements HttpGetActionInterface
{




    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;


    /**
     * @var CheckoutSession;
     */
    private $session;




    public function __construct(

        Context                    $context,
        ScopeConfigInterface       $scopeConfig,
        CheckoutSession            $session


    )
    {

        $this->session = $session;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function execute()
    {

        if ($this->session->isLoggedIn()) {

        if ($this->scopeConfig->isSetFlag('my_config/general/enabled')) {
            return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        }} else {
            die('Sorry, not registration');
        }
    }
}
