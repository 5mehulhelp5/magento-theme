<?php

namespace Forever\Core\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Style implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'style-1', 'label' => __('Style 1')],
            ['value' => 'style-2', 'label' => __('Style 2')],
            ['value' => 'style-3', 'label' => __('Style 3')],
            ['value' => 'style-4', 'label' => __('Style 4')]
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
            'style-1' => __('Style 1'),
            'style-2' => __('Style 2'),
            'style-3' => __('Style 3'),
            'style-4' => __('Style 4')
        ];
    }
}
