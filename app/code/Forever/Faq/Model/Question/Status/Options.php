<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Faq\Model\Question\Status;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Grid display options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('Disabled')],
            ['value' => '1', 'label' => __('Enabled')]
        ];
    }
}
