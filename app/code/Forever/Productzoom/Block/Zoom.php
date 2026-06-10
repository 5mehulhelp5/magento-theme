<?php

declare(strict_types=1);

namespace Forever\Productzoom\Block;

use Forever\Productzoom\Helper\Data as ZoomHelper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Zoom extends Template
{
    public function __construct(
        Context $context,
        private readonly ZoomHelper $zoomHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Pass helper to template via getData('helper')
     */
    protected function _beforeToHtml(): static
    {
        $this->setData('helper', $this->zoomHelper);
        return parent::_beforeToHtml();
    }
}
