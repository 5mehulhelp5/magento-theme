<?php

declare(strict_types=1);

namespace Forever\LayeredNavigation\ViewModel;

use Forever\LayeredNavigation\Helper\Configdata;
use Magento\Catalog\Helper\Data as CatalogData;
use Magento\Framework\Json\Helper\Data as JsonData;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Tax\Helper\Data as TaxData;

class PricesliderViewModel implements ArgumentInterface
{
    /** @var Configdata */
    protected $_moduleHelper;

    /** @var CatalogData */
    protected $_data;

    /** @var TaxData */
    protected $texdata;

    /** @var JsonData */
    protected $jsondata;

    public function __construct(
        CatalogData $data,
        TaxData $texdata,
        JsonData $jsondata,
        Configdata $moduleHelper
    ) {
        $this->_data = $data;
        $this->texdata = $texdata;
        $this->jsondata = $jsondata;
        $this->_moduleHelper = $moduleHelper;
    }

    public function shouldDisplayProductCountOnLayer(): bool
    {
        return (bool)$this->_data->shouldDisplayProductCountOnLayer();
    }

    public function getPriceFormat($value)
    {
        return $this->texdata->getPriceFormat($value);
    }

    public function getjsondata(): JsonData
    {
        return $this->jsondata;
    }

    public function getSlider(): bool
    {
        return (bool)$this->_moduleHelper->isEnabled();
    }

    public function getParseurl(string $parseUrl): array
    {
        return parse_url($parseUrl) ?: [];
    }
}
