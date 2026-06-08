<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Faq\Model\ResourceModel\Question;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Forever\Faq\Model\Question;
use Forever\Faq\Model\ResourceModel\Question as QuestionResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init(
            Question::class,
            QuestionResource::class
        );
    }

    /**
     * Initialize select object
     *
     * @return Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addFilterToMap('id', 'main_table.id');
        $this->getSelect()->columns(
            [
                'id' => 'main_table.id',
                'created_at' => 'main_table.created_at',
                'updated_at' => 'main_table.updated_at',
                'status' => 'main_table.status'
            ]
        );

        return $this;
    }
}
