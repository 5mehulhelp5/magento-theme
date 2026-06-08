<?php

namespace Forever\Testimonials\Model\ResourceModel;

/**
 * Modulename Entity mysql resource.
 */
class Testimonials extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('forever_testimonials', 'id');
    }
}
