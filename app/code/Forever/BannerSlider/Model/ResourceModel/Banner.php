<?php

namespace Forever\BannerSlider\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Banner extends AbstractDb
{
    const TABLE_NAME = 'bannerslider';
    const TABLE_IDENTIFIER = 'id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::TABLE_IDENTIFIER);
    }
}
