<?php
namespace Forever\Core\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductNavigation implements ArgumentInterface
{
    const PRODUCT_THUMBNAIL_IMAGE_ID = 'product_thumbnail_image';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $nextPrevious;

    /**
     * @var Magento\Catalog\Model\Category
     */
    protected $categoryModel;

    /**
     * @var Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @param Psr\Log\LoggerInterface $logger,
     * @param Magento\Catalog\Model\Category $categoryModel
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Magento\Catalog\Helper\Image $imageHelper
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\Category $categoryModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Image $imageHelper
    ) {
        $this->logger = $logger;
        $this->categoryModel = $categoryModel;
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
    }

    /**
     * get all the products ids whithin current category
     *
     * @param object $category
     * @return array $nextPrevious
     */
    public function getCategoryProductIds($category)
    {
        $categoryProducts = $category->getProductCollection()->addAttributeToSelect('*');
        $this->_productCollection = $categoryProducts;
        foreach ($categoryProducts as $product) {
            $this->nextPrevious[$product->getId()] = $product;
        }
        return $this->nextPrevious;
    }

    /**
     * get current category which assign to product
     *
     * @param object $product
     * @return mixed $category
     */
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
            return;
        }
        return $currentCategory;
    }

    /**
     * get all the previous and next products of current product
     *
     * @param object $product
     * @return array $previousAndNext
     */
    public function getPreviousAndNext($product)
    {
        $previousAndNext = [];
        if (!$this->nextPrevious) {
            $currentCategory = $this->getCurrentCategory($product);
            if (!$currentCategory) {
                return;
            }
            $this->nextPrevious = $this->getCategoryProductIds($currentCategory);
        }
        $productId = $product->getId();
        $nextPrevious = $this->nextPrevious;
        if (!$nextPrevious) {
            return;
        }
        $prevProduct  = '';
        foreach ($nextPrevious as $id => $product) {
            if ($id == $productId) {
                break;
            };
            $prevProduct = $product;
            next($nextPrevious);
        }
        $previousAndNext[] = $prevProduct;
        $previousAndNext[] = next($nextPrevious);
        return $previousAndNext;
    }

    /**
     * get previous product of current product based on product id
     *
     * @param object $product
     * @return mixed $product
     */
    public function getPrevProduct($product)
    {
        $previousAndNext = $this->getPreviousAndNext($product);
        $product = $previousAndNext ? current($previousAndNext) : '';
        return $product;
    }
    
    /**
     * get next product of current product based on product id
     *
     * @param object $product
     * @return mixed $product
     */
    public function getNextProduct($product)
    {
        $previousAndNext = $this->getPreviousAndNext($product);
        $product = $previousAndNext ? next($previousAndNext) : '';
        return $product;
    }

    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
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
