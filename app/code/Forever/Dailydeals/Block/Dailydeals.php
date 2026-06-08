<?php

namespace Forever\Dailydeals\Block;

use Magento\Store\Model\ScopeInterface;

class Dailydeals extends \Magento\Framework\View\Element\Template
{
    const ENABLE = 'dailydeals/general/enabled';
    const PRODUCTSKU = 'dailydeals/general/dailydeals_productsku';
    const EXPDATETIME = 'dailydeals/general/dailydeals_exptime';
    const SALETEXT = 'dailydeals/general/dailydeals_saletext';
    const BUTTONTEXT = 'dailydeals/general/dailydeals_buttontext';
    const THUMBNAIL_IMAGE = 'product_thumbnail_image';
    const TIMEZONE = 'general/locale/timezone';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productObj;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Block\Product\ListProduct
     */
    protected $listProduct;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $productImage;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @param Magento\Backend\Block\Template\Context $context
     * @param Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Magento\Catalog\Model\Product $productObj
     * @param Magento\Catalog\Model\ProductRepository $productRepository
     * @param Magento\Catalog\Block\Product\ListProduct $listProduct
     * @param Magento\Catalog\Helper\Image $productImage
     * @param Magento\Framework\Pricing\Helper\Data $priceHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $productObj,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Block\Product\ListProduct $listProduct,
        \Magento\Catalog\Helper\Image $productImage,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->productObj = $productObj;
        $this->productRepository = $productRepository;
        $this->listProduct = $listProduct;
        $this->productImage = $productImage;
        $this->priceHelper = $priceHelper;
    }

    /**
     * @return Scope Config Value | string
     */
    public function getConfigData($path)
    {
        $value = $this->config->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getStoreId()
        );
        return $value;
    }

    /**
     * @return Scope Config Value | string
     */
    public function getConfigSKU()
    {
        return $this->getConfigData(self::PRODUCTSKU);
    }

    /**
     * @return Scope Config Value | bool
     */
    public function isEnable()
    {
        return $this->getConfigData(self::ENABLE);
    }

    /**
     * @return Scope Config Value | string
     */
    public function getConfigExpDateTime()
    {
        return $this->getConfigData(self::EXPDATETIME);
    }

    /**
     * @return Scope Config Value | string
     */
    public function getConfigSaleText()
    {
        return $this->getConfigData(self::SALETEXT);
    }

    /**
     * @return Scope Config Value | string
     */
    public function getConfigButtonText()
    {
        return $this->getConfigData(self::BUTTONTEXT);
    }

    /**
     * @return Scope Config Value | string
     */
    public function getConfigTimeZone()
    {
        return $this->getConfigData(self::TIMEZONE);
    }

    /**
     * @return Product | object
     */
    public function getSpecificProduct()
    {
        $sku = $this->getConfigSKU();
        if ($this->productObj->getIdBySku($sku)) {
            $product = $this->productRepository->get($sku);
            return $product;
        } else {
            return '';
        }
    }

    /**
     * @return Product Image URL | string
     */
    public function getProductImage($product)
    {
        if ($this->productObj->getIdBySku($this->getConfigSKU())) {
            $imageUrl = $this->productImage->init(
                $product,
                self::THUMBNAIL_IMAGE
            )->setImageFile(
                $product->getFile()
            )->resize(
                200,
                200
            )->getUrl();
            return $imageUrl;
        } else {
            return '';
        }
    }

    /**
     * @return Cart URL | string
     */
    public function getAddCartUrl($product)
    {
        if ($this->productObj->getIdBySku($this->getConfigSKU())) {
            return $this->listProduct->getAddToCartUrl($product);
        } else {
            return '';
        }
    }

    /**
     * @return Formatted Price
     */
    public function getFormatedPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    /**
     * @return bool
     */
    public function isProductAvailable()
    {
        if ($this->productObj->getIdBySku($this->getConfigSKU())) {
            return true;
        } else {
            return false;
        }
    }
}
