<?php

namespace Forever\Megamenu\Model\ResourceModel\Megamenu;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init(\Forever\Megamenu\Model\Megamenu::class, \Forever\Megamenu\Model\ResourceModel\Megamenu::class);
    }
}
