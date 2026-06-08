<?php

namespace Forever\Core\Block;

use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;

class CategoryList extends \Magento\Framework\View\Element\Template
{
    const ISENABLE = 'themedesign/imageswitcher/enable';
    const XML_PATH_NEW_ARRIVAL = 'forever_categories/general/enabled';
    const CATEGORIES_SELECT ='forever_categories/home_page/category_select';
    const XML_PATH_CART = 'checkout/cart/redirect_to_cart';
    
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    protected $categoryFactory;
    
    /**
     * @var \Magento\Catalog\Model\Config\Source\Category
     */
    protected $category;

    /**
     * @var Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $productCollection;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $helperData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Forever\Productlabel\ViewModel\ProductLabelViewModel
     */
    protected $productLabelViewModel;

    /**
     * @param Context $context
     * @param Context $gridcontext
     * @param Resolver $layerResolver
     * @param ListProduct $listProductBlock
     * @param ScopeConfigInterface $scopeConfig
     * @param Category $category
     * @param CategoryFactory $categoryFactory
     * @param array $data $productCollectionFactory
     * @param OutputHelper|null $outputHelper
     * @param Magento\Catalog\Helper\Image $helperData
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Forever\Productlabel\ViewModel\ProductLabelViewModel
     * @param Forever\AuthenticationPopUp\ViewModel\AuthenticationViewModel
     */

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Block\Product\Context $gridcontext,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Forever\Core\Model\Config\Category $category,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Helper\Image $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Forever\Productlabel\ViewModel\ProductLabelViewModel $productLabelViewModel,
        \Forever\AuthenticationPopUp\ViewModel\AuthenticationViewModel $authenticationviewmodel,
        array $data = []
    ) {
        
        $this->listProductBlock = $listProductBlock;
        $this->scopeConfig = $scopeConfig;
        $this->category = $category;
        $this->compareProduct = $gridcontext->getCompareProduct();
        $this->wishlistHelper = $gridcontext->getWishlistHelper();
        $this->categoryFactory = $categoryFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->helperData = $helperData;
        $this->storeManager = $storeManager;
        $this->productLabelViewModel = $productLabelViewModel;
        $this->authenticationviewmodel = $authenticationviewmodel;
        parent::__construct($context, $data);
    }

    /**
     * Check if the module has been enabled in the admin
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NEW_ARRIVAL,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isRedirectToCartEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CART,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getAddToCartPostParams($product)
    {
        return $this->listProductBlock->getAddToCartPostParams($product);
    }

    public function getAddToWishlistParams($product)
    {
        return $this->wishlistHelper->getAddParams($product);
    }
    
    /**
     * @return \Magento\Catalog\Helper\Product\Compare
     * @since 101.0.1
     */
    public function getCompareHelper()
    {
        return $this->compareProduct;
    }

    public function getSelectedCategory()
    {
        return $this->category->getSelected(
            $this->_scopeConfig->getValue(
                self::CATEGORIES_SELECT,
                ScopeInterface::SCOPE_STORE
            )
        );
    }

    /**
     * return detail of products
     *
     * @return html
     **/
    public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product)
    {
        $renderer = $this->getDetailsRenderer($product->getTypeId());
        if ($renderer) {
            $renderer->setProduct($product);
            return $renderer->toHtml();
        }
        return '';
    }

    /**
     * return detail renderer
     *
     * @return rendererlist
     **/
    public function getDetailsRenderer($type = null)
    {
        if ($type === null) {
            $type = 'default';
        }
        $rendererList = $this->getDetailsRendererList();
        if ($rendererList) {
            return $rendererList->getRenderer($type, 'default');
        }
        return null;
    }
    
    protected function getDetailsRendererList()
    {
        return $this->getDetailsRendererListName() ? $this->getLayout()->getBlock(
            $this->getDetailsRendererListName()
        ) : $this->getChildBlock(
            'homepage.toprenderers'
        );
    }
    
    public function getProductPricetoHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null
    ) {
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product
            );
        }
        return $price;
    }

    public function getCategoryProducts($categoryId)
    {
        $ids = $this->getSelectedCategoryIds();
        $products = clone $this->getProductCollection();
        $products->addCategoriesFilter(['eq' => $categoryId]);
        $products->addAttributeToSort('created_at', 'DESC');
        return $products;
    }

    public function getProductCollection()
    {
        if (!$this->productCollection) {
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->addAttributeToSelect('*');
            $productCollection->addAttributeToFilter(
                'visibility',
                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
            );
            $productCollection->addAttributeToFilter(
                'status',
                \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
            );
            $this->productCollection = $productCollection;
        }
        return $this->productCollection;
    }

    public function getSelectedCategoryIds()
    {
        return $this->category->getSelectedCategoryByIds(
            $this->_scopeConfig->getValue(
                self::CATEGORIES_SELECT,
                ScopeInterface::SCOPE_STORE
            )
        );
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
    public function getImageSwitcherConfigValue()
    {
        $value = $this->scopeConfig->getValue(
            self::ISENABLE,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getStoreId()
        );
        return $value;
    }

    /**
     * Get product label
     *
     * @return array
     */
    public function getProductlabel($product)
    {
        return $this->productLabelViewModel->getProductlabel($product);
    }

    /**
     * Get store config value
     *
     * @return array
     */
    public function getScopeconfig($value)
    {

        return $this->productLabelViewModel->getScopeconfig($value);
    }

    public function getAuthenticationpopup()
    {
        
        return $this->authenticationviewmodel->getScopeconfig();
    }
}
