<?php

declare(strict_types=1);

namespace Forever\Faq\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Question extends AbstractDb
{
    public const TABLE_NAME = 'forever_faq_question';

    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, 'id');
    }
}
