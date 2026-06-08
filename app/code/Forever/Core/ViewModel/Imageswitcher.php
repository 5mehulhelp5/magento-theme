<?php

namespace Forever\Core\ViewModel;

use Magento\Store\Model\ScopeInterface;

class Imageswitcher implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    const ISENABLE = 'themedesign/imageswitcher/enable';

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Block\Product\Image
     */
    protected $image;

    /**
     * @param Magento\Catalog\Helper\Image $helperData
     * @param Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Block\Product\Image $image
     */
    public function __construct(
        \Magento\Catalog\Helper\Image $helperData,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Catalog\Block\Product\Image $image,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->helperData = $helperData;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->image = $image;
    }

    /**
     * Get Image URL
     *
     * @return Image URL | string
     */
    public function getImageUrl($_product)
    {
        $productImage = $this->helperData->init(
            $_product,
            'image'
        )->setImageFile(
            $_product->getImage()
        );
        $productImageUrl = $productImage->getUrl();
        return $productImageUrl;
    }

    /**
     * Get Config Value
     *
     * @return bool
     */
    public function getConfigData()
    {
        $value = $this->config->getValue(
            self::ISENABLE,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getStoreId()
        );
        return $value;
    }
    public function getImageWidth($product)
    {
        $imageId = 'category_page_grid';
        $attributes = [];
        return $this->helperData->init($product, $imageId, $attributes)->getWidth();
    }
    public function getImagePadding($product)
    {
        $imageId = 'category_page_grid';
        $attributes = [];
        $width = $this->helperData->init($product, $imageId, $attributes)->getWidth();
        $height = $this->helperData->init($product, $imageId, $attributes)->getHeight();
        if ($width && $height) {
            return $height / $width;
        }
        return 1;
    }
    public function getImageHeight($product)
    {
        $imageId = 'category_page_grid';
        $attributes = [];
        return $this->helperData->init($product, $imageId, $attributes)->getHeight();
    }
}
