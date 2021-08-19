<?php

namespace Amasty\SecondModule\Block;


use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;


class Index extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(

        Template\Context     $context,
        ScopeConfigInterface $scopeConfig,
        array                $data = []
    )
    {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    public function sayHiconf()
    {
        return $this->scopeConfig->getValue('my_config/general/greeting_text');
    }

    public function isShowInput() {
        return $this->scopeConfig->getValue('my_config/general/qrt_enabled');
    }

    public function getQRTNumber() {
        return $this->scopeConfig->getValue('my_config/general/qrt_number');
    }

}
