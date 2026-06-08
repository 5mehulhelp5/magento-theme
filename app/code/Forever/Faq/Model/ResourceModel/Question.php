<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Faq\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Question extends AbstractDb
{
    const TABLE_NAME = 'forever_faq_question';

    protected function _construct()
    {
        $this->_init(static::TABLE_NAME, 'id');
    }
}
