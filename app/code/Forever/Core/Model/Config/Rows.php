<?php
namespace Forever\Core\Model\Config;

class Rows implements \Magento\Framework\Option\ArrayInterface
{
    /*
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('6')],
            ['value' => 1, 'label' => __('8')]
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
            0 => __('6'),
            1 => __('8')
        ];
    }
}
