<?php

declare(strict_types=1);

namespace Forever\Dailydeals\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Stdlib\DateTime as MagentoDateTime;

class DateTime extends Field
{
    /**
     * Render the datetime field with date and time format set
     */
    public function render(AbstractElement $element): string
    {
        $element->setDateFormat(MagentoDateTime::DATE_INTERNAL_FORMAT);
        $element->setTimeFormat('HH:mm:ss');
        return parent::render($element);
    }
}
