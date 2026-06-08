<?php

namespace Forever\Dailydeals\Block\Adminhtml;

class DateTime extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Get Element
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return Element
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setDateFormat(\Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT);
        $element->setTimeFormat("HH:mm:ss");
        return parent::render($element);
    }
}
