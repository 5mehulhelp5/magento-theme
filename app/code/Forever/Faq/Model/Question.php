<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Faq\Model;

use Magento\Framework\Model\AbstractModel;

class Question extends AbstractModel
{
    /**
     * Question Model Constructor
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Question::class);
    }
}
