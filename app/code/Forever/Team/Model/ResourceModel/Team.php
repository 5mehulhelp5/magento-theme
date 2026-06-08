<?php

namespace Forever\Team\Model\ResourceModel;

/**
 * Modulename Entity mysql resource.
 */
class Team extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('forever_team', 'id');
    }
}
