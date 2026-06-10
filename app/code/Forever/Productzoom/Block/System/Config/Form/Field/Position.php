<?php

declare(strict_types=1);

namespace Forever\Productzoom\Block\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class Position extends Field
{
    private readonly AssetRepository $assetRepository;

    public function __construct(Context $context, array $data = [])
    {
        $this->assetRepository = $context->getAssetRepository();
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        $html  = $element->getElementHtml();
        $asset = $this->assetRepository->createAsset('Forever_Productzoom::images/window-positions.png');
        $html .= '<div style="position:relative;margin-top:10px"><img id="window-positions" src="'
            . $asset->getUrl() . '" alt="" border="0"></div>';
        return $html;
    }
}
