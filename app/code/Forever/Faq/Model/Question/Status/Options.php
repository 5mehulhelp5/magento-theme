<?php

declare(strict_types=1);

namespace Forever\Faq\Model\Question\Status;

use Magento\Framework\Data\OptionSourceInterface;

class Options implements OptionSourceInterface
{
    /**
     * Grid display options.
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '0', 'label' => __('Disabled')],
            ['value' => '1', 'label' => __('Enabled')],
        ];
    }
}
