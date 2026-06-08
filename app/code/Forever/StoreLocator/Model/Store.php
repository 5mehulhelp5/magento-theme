<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\StoreLocator\Model;

use Forever\StoreLocator\Model\ResourceModel\Store as StoreResourceModel;

class Store extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(StoreResourceModel::class);
    }
}
