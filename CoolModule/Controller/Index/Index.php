<?php

namespace Amasty\CoolModule\Controller\Index;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Index extends Action implements  HttpGetActionInterface
{
public  function execute()
{
    echo 'Привет Magento. Привет, Amasty. Я готов тебя покорить!';
}
}
