<?php
namespace Forever\Core\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Helper\Image;
use Magento\Store\Model\StoreManagerInterface;

class ProductNavigation implements ArgumentInterface
{
    const PRODUCT_THUMBNAIL_IMAGE_ID = 'product_thumbnail_image';

    protected LoggerInterface $logger;
    protected ?array $nextPrevious = null;
    protected Category $categoryModel;
    protected Image $imageHelper;
    protected StoreManagerInterface $storeManager;
    protected $productCollectionResource = null;

    public function __construct(
        LoggerInterface $logger,
        Category $categoryModel,
        StoreManagerInterface $storeManager,
        Image $imageHelper
    ) {
        $this->logger = $logger;
        $this->categoryModel = $categoryModel;
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
    }

    public function getCategoryProductIds($category)
    {
        $categoryProducts = $category->getProductCollection()->addAttributeToSelect('*');
        $this->productCollectionResource = $categoryProducts;
        foreach ($categoryProducts as $product) {
            $this->nextPrevious[$product->getId()] = $product;
        }
        return $this->nextPrevious;
    }

    public function getCurrentCategory($product)
    {
        $currentCategory = $product->getCategory();
        if (!$currentCategory || $currentCategory->getIsActive() == 0) {
            foreach ($product->getCategoryCollection() as $category) {
                $categoryId = $category->getId();
                $currentCategory = $this->categoryModel->load($categoryId);
                if ($currentCategory->getIsActive()) {
                    return $currentCategory;
                }
            }
            return null;
        }
        return $currentCategory;
    }

    public function getPreviousAndNext($product)
    {
        if (!$this->nextPrevious) {
            $currentCategory = $this->getCurrentCategory($product);
            if (!$currentCategory) {
                return null;
            }
            $this->nextPrevious = $this->getCategoryProductIds($currentCategory);
        }
        $productId = $product->getId();
        $nextPrevious = $this->nextPrevious;
        if (!$nextPrevious) {
            return null;
        }
        $prevProduct = '';
        foreach ($nextPrevious as $id => $product) {
            if ($id == $productId) {
                break;
            }
            $prevProduct = $product;
            next($nextPrevious);
        }
        return [$prevProduct, next($nextPrevious)];
    }

    public function getPrevProduct($product)
    {
        $previousAndNext = $this->getPreviousAndNext($product);
        return $previousAndNext ? current($previousAndNext) : '';
    }

    public function getNextProduct($product)
    {
        $previousAndNext = $this->getPreviousAndNext($product);
        return $previousAndNext ? next($previousAndNext) : '';
    }

    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    public function getProductThumbnail($product)
    {
        try {
            return $this->imageHelper
                ->init($product, self::PRODUCT_THUMBNAIL_IMAGE_ID)
                ->getUrl();
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }
        return false;
    }
}
