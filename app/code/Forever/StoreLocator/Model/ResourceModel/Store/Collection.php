<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\StoreLocator\Model\ResourceModel\Store;

use Forever\StoreLocator\Model\Store;
use Forever\StoreLocator\Model\ResourceModel\Store as StoreResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $idFieldName = 'store_id';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Store::class, StoreResourceModel::class);
    }
}
