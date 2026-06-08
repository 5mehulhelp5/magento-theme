<?php

namespace Forever\Brand\Model\ResourceModel;

/**
 * Modulename Entity mysql resource.
 */
class Brand extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('forever_brand', 'id');
    }
}
