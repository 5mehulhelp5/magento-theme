<?php
namespace Forever\Lazyload\Block\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Image\NotLoadInfoImageException;
use Magento\Store\Model\ScopeInterface;

class ImageBuilder extends \Magento\Catalog\Block\Product\ImageBuilder
{
    const MODULE_ENABLE = 'lazyload/general/enable';
    const LAZYLOAD_IMAGES = 'lazyload/general/lazyload_images';
    const LAZYLOAD_PLACEHOLDER = 'lazyload/general/lazyload_placeholder';
    const BASE_IMAGE = 'product_base_image';
    const IMAGE_TEMPLATE = 'Forever_Lazyload::product/image.phtml';
    const IMAGE_BORDER_TEMPLATE = 'Forever_Lazyload::product/image_with_borders.phtml';

    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $helperFactory;

    /**
     * @var \Magento\Catalog\Block\Product\ImageFactory
     */
    protected $imageFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadataInterface;

    /**
     * @param Magento\Catalog\Helper\ImageFactory $helperFactory
     * @param Magento\Catalog\Block\Product\ImageFactory $imageFactory
     * @param Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Magento\Framework\App\ProductMetadataInterface $productMetadataInterface
     */
    public function __construct(
        \Magento\Catalog\Helper\ImageFactory $helperFactory,
        \Magento\Catalog\Block\Product\ImageFactory $imageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ProductMetadataInterface $productMetadataInterface
    ) {
        parent::__construct($helperFactory, $imageFactory);
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->productMetadataInterface = $productMetadataInterface;
    }

    /**
     * Create image block
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function create(?Product $product = null, ?string $imageId = null, ?array $attributes = null)
    {
        $product = $product ? $product : $this->product;
        $imageId = $imageId ? $imageId : $this->imageId;
        $attributes = $attributes ? $attributes : $this->attributes;

        $isEnable = (int) $this->getConfigData(self::MODULE_ENABLE);
        if (!$isEnable || !$this->getConfigData(self::LAZYLOAD_IMAGES) || $imageId === self::BASE_IMAGE) {
            return parent::create($product, $imageId, $attributes);
        }

        /**
         * @var \Magento\Catalog\Helper\Image $helper
         */
        $helper = $this->helperFactory->create()->init($product, $imageId);
        $attributes['data-src'] = $helper->getUrl();
        $this->setAttributes($attributes);

        $template = $helper->getFrame()
            ? self::IMAGE_TEMPLATE
            : self::IMAGE_BORDER_TEMPLATE;

        try {
            $imagesize = $helper->getResizedImageInfo();
        } catch (NotLoadInfoImageException $exception) {
            $imagesize = [$helper->getWidth(), $helper->getHeight()];
        }

        $mediaPath = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $placeHolder = $this->getConfigData(self::LAZYLOAD_PLACEHOLDER);
        $placeHolderUrl = $mediaPath . $placeHolder;

        $data = [
            'template' => $template,
            'image_url' => $placeHolderUrl,
            'width' => $helper->getWidth(),
            'height' => $helper->getHeight(),
            'label' => $helper->getLabel(),
            'ratio' => $this->getRatio($helper),
            'custom_attributes' => $this->getCustomAttributes(),
            'resized_image_width' => $imagesize[0],
            'resized_image_height' => $imagesize[1],
            'product_id' => $product->getId()
        ];

        if ($this->productMetadataInterface->getVersion() < '2.3.0') {
            return $this->imageFactory->create(['data' => $data]);
        } else {
            $helper = $this->imageFactory->create($product, $imageId, $attributes);
            $helper->setData($data);
            return $helper;
        }
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
}
