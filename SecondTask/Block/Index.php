<?php
namespace Amasty\SecondTask\Block;
use Magento\Framework\View\Element\Template;

class Index extends Template {
    public function  sayWazzupTo($name){
        return 'Wazzup, ' . $name;

    }
}
?>
