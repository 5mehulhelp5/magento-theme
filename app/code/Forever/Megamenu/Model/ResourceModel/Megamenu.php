<?php

namespace Forever\Megamenu\Model\ResourceModel;

class Megamenu extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        //megamenu_id to id
        $this->_init('forever_megamenu', 'id');
    }
}
