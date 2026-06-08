<?php
/**
 * @Author: Alex Dong
 */

namespace Forever\Productzoom\Block;

use Forever\Productzoom\Helper\Data as ZoomHelper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Zoom extends Template
{
    /**
     * @var ZoomHelper
     */
    protected $zoomHelper;

    public function __construct(
        Context $context,
        ZoomHelper $zoomHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->zoomHelper = $zoomHelper;
    }

    public function getZoomHelper()
    {
        return $this->zoomHelper;
    }
}
