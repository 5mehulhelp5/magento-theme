<?php

namespace Forever\Core\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Sticky implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'sticky-1', 'label' => __('Sticky Header 1')],
            ['value' => 'sticky-2', 'label' => __('Sticky Header 2')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'sticky-1' => __('Sticky Header 1'),
            'sticky-1' => __('Sticky Header 2')
        ];
    }
}
