<?php

declare(strict_types=1);

namespace Forever\Faq\Model;

use Magento\Framework\Model\AbstractModel;

class Question extends AbstractModel
{
    /**
     * Question Model Constructor
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel\Question::class);
    }
}
