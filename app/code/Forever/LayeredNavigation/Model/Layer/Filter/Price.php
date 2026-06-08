<?php

declare(strict_types=1);

namespace Forever\LayeredNavigation\Model\Layer\Filter;

use Magento\CatalogSearch\Model\Layer\Filter\Price as AbstractFilter;

class Price extends AbstractFilter
{
    /** @var \Magento\Catalog\Model\Layer\Filter\DataProvider\Price */
    private $dataProvider;

    /** @var \Magento\Framework\Pricing\PriceCurrencyInterface */
    private $priceCurrency;

    /** @var \Forever\LayeredNavigation\Helper\Configdata */
    protected $_moduleHelper;

    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
        \Forever\LayeredNavigation\Helper\Configdata $moduleHelper,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $resource,
            $customerSession,
            $priceAlgorithm,
            $priceCurrency,
            $algorithmFactory,
            $dataProviderFactory,
            $data
        );

        $this->priceCurrency = $priceCurrency;
        $this->dataProvider = $dataProviderFactory->create(['layer' => $this->getLayer()]);
        $this->_moduleHelper = $moduleHelper;
    }

    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->_moduleHelper->isEnabled()) {
            return parent::apply($request);
        }

        $filter = $request->getParam($this->getRequestVar());
        if (!$filter || is_array($filter)) {
            return $this;
        }

        $filterParams = explode(',', (string)$filter);
        $filter = $this->dataProvider->validateFilter($filterParams[0]);
        if (!$filter) {
            return $this;
        }

        $this->dataProvider->setInterval($filter);
        $priorFilters = $this->dataProvider->getPriorFilters($filterParams);
        if ($priorFilters) {
            $this->dataProvider->setPriorIntervals($priorFilters);
        }

        [$from, $to] = $filter;
        $currencyRate = (float)$this->getCurrencyRate();
        $currencyRate = $currencyRate > 0 ? $currencyRate : 1.0;

        $fromBase = is_numeric($from) ? (float)$from / $currencyRate : 0.0;
        $toBase = is_numeric($to) ? (float)$to / $currencyRate : '';

        $this->getLayer()->getProductCollection()->addFieldToFilter(
            'price',
            ['from' => $fromBase, 'to' => $toBase === '' || $fromBase == $toBase ? $toBase : $toBase - self::PRICE_DELTA]
        );

        $this->getLayer()->getState()->addFilter(
            $this->_createItem($this->_renderRangeLabel(empty($from) ? 0 : $from, $to), $filter)
        );

        return $this;
    }

    protected function _renderRangeLabel($fromPrice, $toPrice, $isLast = false)
    {
        if (!$this->_moduleHelper->isEnabled()) {
            return parent::_renderRangeLabel($fromPrice, $toPrice, $isLast);
        }

        $formattedFromPrice = $this->priceCurrency->format((float)$fromPrice);
        if ($isLast || $toPrice === '') {
            return __('%1 and above', $formattedFromPrice);
        }

        if ($fromPrice == $toPrice && $this->dataProvider->getOnePriceIntervalValue()) {
            return $formattedFromPrice;
        }

        return __('%1 - %2', $formattedFromPrice, $this->priceCurrency->format((float)$toPrice));
    }

    protected function _getItemsData()
    {
        if (!$this->_moduleHelper->isEnabled()) {
            return parent::_getItemsData();
        }

        return [[
            'label' => __('Price'),
            'value' => '0-100',
            'count' => max(1, (int)$this->getLayer()->getProductCollection()->getSize()),
            'from' => '0',
            'to' => '100',
        ]];
    }
}
