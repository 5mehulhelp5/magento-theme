<?php

declare(strict_types=1);

/**
 * @Author: nguyen
 * @Date:   2020-06-09 20:10:33
 * @Last Modified by:   nguyen
 * @Last Modified time: 2020-07-09 22:33:39
 */

namespace Forever\Productzoom\Model\Config\Source;

class Type implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            'window' 	=> 'Window',
            'inner' 	=> 'Inner',
            'lens' 		=> 'Lens'
        ];
    }
}
