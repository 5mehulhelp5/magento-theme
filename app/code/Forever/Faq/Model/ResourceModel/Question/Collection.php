<?php

declare(strict_types=1);

namespace Forever\Faq\Model\ResourceModel\Question;

use Forever\Faq\Model\Question;
use Forever\Faq\Model\ResourceModel\Question as QuestionResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    protected function _construct(): void
    {
        $this->_init(Question::class, QuestionResource::class);
    }

    /**
     * Initialize select object
     */
    protected function _initSelect(): static
    {
        parent::_initSelect();

        $this->addFilterToMap('id', 'main_table.id');
        $this->getSelect()->columns([
            'id'         => 'main_table.id',
            'created_at' => 'main_table.created_at',
            'updated_at' => 'main_table.updated_at',
            'status'     => 'main_table.status',
        ]);

        return $this;
    }
}
