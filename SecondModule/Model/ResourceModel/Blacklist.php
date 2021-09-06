<?php

namespace Amasty\SecondModule\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;


class Blacklist extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\SecondModule\Setup\InstallSchema::TABLE_NAME,
            'blacklist_id'
        );
    }
}
