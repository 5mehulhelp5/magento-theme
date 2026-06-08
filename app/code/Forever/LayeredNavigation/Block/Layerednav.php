<?php

declare(strict_types=1);

namespace Forever\LayeredNavigation\Block;

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Layerednav extends Template
{
    /** @var Registry */
    protected $_registry;

    /** @var CollectionFactory */
    protected $_productCollectionFactory;

    /** @var Visibility */
    protected $_catalogProductVisibility;

    /** @var Collection */
    protected $_ProductCollection;

    /** @var ProductFactory */
    protected $_productFactory;

    /** @var LayerResolver|null */
    private $layerResolver;

    public function __construct(
        Context $context,
        Registry $registry,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        Collection $ProductCollection,
        ProductFactory $productFactory,
        ?LayerResolver $layerResolver = null,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_ProductCollection = $ProductCollection;
        $this->_productFactory = $productFactory;
        $this->layerResolver = $layerResolver;
        parent::__construct($context, $data);
    }

    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

    public function getCurrencySymbol(): string
    {
        return (string)$this->_storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
    }

    /**
     * @return array{min:int,max:int}|false
     */
    public function getCurrentCategoryMaxPrice()
    {
        $collection = null;
        $currentCategory = $this->getCurrentCategory();

        if ($currentCategory) {
            $collection = $this->_productCollectionFactory->create()
                ->addAttributeToSelect(['price', 'special_price', 'type_id'])
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addCategoryFilter($currentCategory)
                ->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        } elseif ($this->layerResolver) {
            try {
                $collection = clone $this->layerResolver->get()->getProductCollection();
            } catch (\Throwable $e) {
                $collection = null;
            }
        }

        if (!$collection) {
            return false;
        }

        $min = null;
        $max = null;
        foreach ($collection as $product) {
            $price = $product->getFinalPrice();
            if (in_array($product->getTypeId(), ['bundle', 'grouped'], true)) {
                $price = $product->getMinPrice();
            }
            if (!is_numeric($price)) {
                continue;
            }
            $price = (float)$price;
            $min = $min === null ? $price : min($min, $price);
            $max = $max === null ? $price : max($max, $price);
        }

        if ($min === null || $max === null) {
            return false;
        }

        $currencyRate = (float)$this->getCurrencyRate();

        return [
            'min' => (int)floor($min * $currencyRate),
            'max' => (int)ceil($max * $currencyRate)
        ];
    }

    public function getCurrencyRate(): float
    {
        $rate = $this->_getData('currency_rate');
        if ($rate === null) {
            $rate = $this->_storeManager->getStore()->getCurrentCurrencyRate();
        }

        return $rate ? (float)$rate : 1.0;
    }
}
