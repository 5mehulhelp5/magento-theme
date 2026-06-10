<?php

declare(strict_types=1);

/**
 * @Author: nguyen
 * @Date:   2020-06-09 20:10:33
 * @Last Modified by:   nguyen
 * @Last Modified time: 2020-07-09 22:34:47
 */

namespace Forever\Productzoom\Model\Config\Source;

class Lensshape implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            'square' 	=> 'Square',
            'round' 	=> 'Round'
        ];
    }
}
