<?php
namespace Forever\Lazyload\Model\Config\Source;

class Effect implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'show',
                'label' => __('Show')
            ],
            [
                'value' => 'fadeIn',
                'label' => __('FadeIn')
            ]
        ];
    }
}
