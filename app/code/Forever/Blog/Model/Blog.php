<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Blog\Model;

use Forever\Blog\Model\ResourceModel\Blog as BlogResourceModel;
use Magento\Framework\Model\AbstractModel;

class Blog extends AbstractModel
{
    /**
     * @return construct
     */
    protected function _construct()
    {
        $this->_init(BlogResourceModel::class);
    }
}
