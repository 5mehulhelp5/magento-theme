<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Blog\Model\ResourceModel\Blog;

use Forever\Blog\Model\Blog;
use Forever\Blog\Model\ResourceModel\Blog as BlogResourceModel;

/**
 * @Class Collection
 * package Forever\Blog\Model\ResourceModel\Traffic
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected $idFieldName = 'blog_id';
    /**
     * @return construct
     */
    protected function _construct()
    {
        $this->_init(Blog::class, BlogResourceModel::class);
    }
}
