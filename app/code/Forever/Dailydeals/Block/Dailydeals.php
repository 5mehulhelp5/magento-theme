<?php

declare(strict_types=1);

namespace Forever\Dailydeals\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Dailydeals extends Template
{
    public const ENABLE = 'dailydeals/general/enabled';
    public const PRODUCTSKU = 'dailydeals/general/dailydeals_productsku';
    public const EXPDATETIME = 'dailydeals/general/dailydeals_exptime';
    public const SALETEXT = 'dailydeals/general/dailydeals_saletext';
    public const BUTTONTEXT = 'dailydeals/general/dailydeals_buttontext';
    public const THUMBNAIL_IMAGE = 'product_thumbnail_image';
    public const TIMEZONE = 'general/locale/timezone';

    public function __construct(
        Context $context,
        protected readonly ScopeConfigInterface $config,
        protected readonly StoreManagerInterface $storeManager,
        protected readonly Product $productObj,
        protected readonly ProductRepository $productRepository,
        protected readonly ListProduct $listProduct,
        protected readonly Image $productImage,
        protected readonly PriceHelper $priceHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get store config value
     */
    public function getConfigData(string $path): mixed
    {
        return $this->config->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getStoreId()
        );
    }

    public function getConfigSKU(): ?string
    {
        return $this->getConfigData(self::PRODUCTSKU);
    }

    public function isEnable(): bool
    {
        return (bool) $this->getConfigData(self::ENABLE);
    }

    public function getConfigExpDateTime(): ?string
    {
        return $this->getConfigData(self::EXPDATETIME);
    }

    public function getConfigSaleText(): ?string
    {
        return $this->getConfigData(self::SALETEXT);
    }

    public function getConfigButtonText(): ?string
    {
        return $this->getConfigData(self::BUTTONTEXT);
    }

    public function getConfigTimeZone(): ?string
    {
        return $this->getConfigData(self::TIMEZONE);
    }

    /**
     * @return Product|string
     */
    public function getSpecificProduct(): Product|string
    {
        $sku = $this->getConfigSKU();
        if ($sku && $this->productObj->getIdBySku($sku)) {
            try {
                return $this->productRepository->get($sku);
            } catch (NoSuchEntityException $e) {
                return '';
            }
        }
        return '';
    }

    /**
     * Get product image URL
     */
    public function getProductImage(Product $product): string
    {
        $sku = $this->getConfigSKU();
        if ($sku && $this->productObj->getIdBySku($sku)) {
            return $this->productImage->init(
                $product,
                self::THUMBNAIL_IMAGE
            )->resize(200, 200)->getUrl();
        }
        return '';
    }

    /**
     * Get add-to-cart URL
     */
    public function getAddCartUrl(Product $product): string
    {
        $sku = $this->getConfigSKU();
        if ($sku && $this->productObj->getIdBySku($sku)) {
            return $this->listProduct->getAddToCartUrl($product);
        }
        return '';
    }

    /**
     * Format price with currency
     */
    public function getFormatedPrice(float|int|string $price): string
    {
        return (string) $this->priceHelper->currency($price, true, false);
    }

    /**
     * Check if the configured product exists
     */
    public function isProductAvailable(): bool
    {
        $sku = $this->getConfigSKU();
        return $sku !== null && $sku !== '' && (bool) $this->productObj->getIdBySku($sku);
    }
}
