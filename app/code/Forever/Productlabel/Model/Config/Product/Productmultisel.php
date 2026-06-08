<?php

declare(strict_types=1);

namespace Forever\Productlabel\Model\Config\Product;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

class Productmultisel extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions(): array
    {
        return [
            ['value' => 1, 'label' => __('New Arrival')],
            ['value' => 2, 'label' => __('Discount')],
            ['value' => 3, 'label' => __('Best Seller')]
        ];
    }

    /**
     * Get All Multi Select Options as string
     *
     * @param string $optionIds
     * @return string
     */
    public function getOptionsMulti(string $optionIds): string
    {
        $entries = explode(',', $optionIds);
        $option = $this->getAllOptions();
        $result = [];
        foreach ($option as $key => $value) {
            if (in_array((string)$key, $entries)) {
                $result[] = $value['label'];
            }
        }
        return implode(', ', $result);
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
