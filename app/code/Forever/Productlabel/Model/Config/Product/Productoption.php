<?php

declare(strict_types=1);

namespace Forever\Productlabel\Model\Config\Product;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Data\OptionSourceInterface;

class Productoption extends AbstractSource implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function getAllOptions(): array
    {
        return [
            ['value' => 1, 'label' => __('Top Left')],
            ['value' => 2, 'label' => __('Top Right')],
        ];
    }

    /**
     * Required by OptionSourceInterface
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return $this->getAllOptions();
    }
}
