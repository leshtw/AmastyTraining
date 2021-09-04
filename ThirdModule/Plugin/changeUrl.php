<?php
namespace Amasty\ThirdModule\Plugin;

class changeUrl
{
    public function aroundGetFormAction($subject)
    {
        return 'checkout/cart/add';
    }
}
