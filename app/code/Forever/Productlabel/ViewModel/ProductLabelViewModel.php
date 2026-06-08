<?php

declare(strict_types=1);

namespace Forever\Productlabel\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Store\Model\ScopeInterface;

class ProductLabelViewModel implements ArgumentInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeconfig;

    /**
     * @var ProductResource
     */
    protected $productResource;

    /**
     * @param ScopeConfigInterface $scopeconfig
     * @param ProductResource $productResource
     */
    public function __construct(
        ScopeConfigInterface $scopeconfig,
        ProductResource $productResource
    ) {
        $this->scopeconfig = $scopeconfig;
        $this->productResource = $productResource;
    }

    /**
     * Returns product label array with discount % if applicable.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array|null
     */
    public function getProductlabel($product): ?array
    {
        $rawValue = $product->getProductlabel();
        if (!$rawValue) {
            return null;
        }

        $alllabel = explode(',', (string)$rawValue);
        $productlabAttr = $this->productResource->getAttribute('productlabel');
        $allproductlabel = [];

        if ($productlabAttr && $productlabAttr->usesSource()) {
            foreach ($alllabel as $value) {
                $text = $productlabAttr->getSource()->getOptionText(trim($value));
                $allproductlabel[] = (string)$text;
            }
        }

        if (empty($allproductlabel)) {
            return null;
        }

        $discountper = 0;
        $specialPrice = (float)$product->getSpecialPrice();
        $price = (float)$product->getPrice();

        if ($specialPrice > 0 && $price > 0) {
            $discountper = (int)round(100 - ($specialPrice / $price) * 100);
        }

        $labelUpdate = $allproductlabel;
        foreach ($allproductlabel as $k => $label) {
            if ($label === 'Discount') {
                if ($specialPrice > 0 && $discountper > 0) {
                    $labelUpdate[$k] = (string)$discountper;
                } else {
                    unset($labelUpdate[$k]);
                }
            }
        }

        return array_values($labelUpdate);
    }

    /**
     * Return Scope Config Value
     *
     * @param string $value
     * @return mixed
     */
    public function getScopeconfig(string $value)
    {
        return $this->scopeconfig->getValue(
            $value,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return product position option as CSS-safe string.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string|null
     */
    public function getProductoption($product): ?string
    {
        $select = $product->getProductoption();
        if (!$select) {
            return null;
        }

        $selectAttr = $this->productResource->getAttribute('productoption');
        if ($selectAttr && $selectAttr->usesSource()) {
            $text = $selectAttr->getSource()->getOptionText($select);
            return strtolower(str_replace(' ', '_', (string)$text));
        }

        return null;
    }
}
