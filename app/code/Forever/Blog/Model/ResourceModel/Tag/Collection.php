<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Blog\Model\ResourceModel\Tag;

use Forever\Blog\Model\Tag;
use Forever\Blog\Model\ResourceModel\Tag as TagResourceModel;

/**
 * @Class Collection
 * package Forever\Blog\Model\ResourceModel\Traffic
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected $idFieldName = 'tag_id';
    /**
     * @return construct
     */
    protected function _construct()
    {
        $this->_init(Tag::class, TagResourceModel::class);
    }
}
