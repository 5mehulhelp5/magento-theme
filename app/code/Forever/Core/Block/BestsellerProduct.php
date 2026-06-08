<?php
namespace Forever\Core\Block;

use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Model\Product as ModelProduct;

class BestsellerProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    const ISENABLE = 'themedesign/imageswitcher/enable';
    const XML_PATH_BESTSELLER = 'bestseller/general/enable';
    const XML_PRODUCT_ROW ='bestseller/general/productscount';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory
     */
    protected $bestSellersCollectionFactory;

    /**
     * @var Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * Template processor instance
     *
     * @var Template
     */
    protected $templateProcessor = null;

    /**
     * Eav config
     *
     * @var Config
     */
    protected $eavConfig;
   /**
    * @var \Forever\BestSeller\Model\Config\Source\Row
    */
    protected $row;

    /**
     * @var Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $helperData;

    /**
     * @var \Forever\Productlabel\ViewModel\ProductLabelViewModel
     */
    protected $productLabelViewModel;

    /**
     * @param Magento\Catalog\Block\Product\Context $context
     * @param Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param Magento\Framework\Url\Helper\Data $urlHelper
     * @param Magento\Customer\Model\Session $customerSession
     * @param Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $bestSellersCollectionFactory
     * @param Forever\Core\Model\Config\Rows $row
     * @param Psr\Log\LoggerInterface $logger
     * @param Context $gridcontext
     * @param Magento\Catalog\Helper\Image $helperData
     * @param Forever\Productlabel\ViewModel\ProductLabelViewModel
     * @param Forever\AuthenticationPopUp\ViewModel\AuthenticationViewModel
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $bestSellersCollectionFactory,
        \Forever\Core\Model\Config\Rows $row,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Escaper $escaper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Block\Product\Context $gridcontext,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        \Magento\Catalog\Helper\Image $helperData,
        \Forever\Productlabel\ViewModel\ProductLabelViewModel $productLabelViewModel,
        \Forever\AuthenticationPopUp\ViewModel\AuthenticationViewModel $authenticationviewmodel,
        array $data = []
    ) {
        
        $this->customerSession = $customerSession;
        $this->categoryFactory = $categoryFactory;
        $this->compareProduct = $context->getCompareProduct();
        $this->bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->storeManager = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->eavConfig = $eavConfig;
        $this->escaper = $escaper;
        $this->listProductBlock = $listProductBlock;
        $this->compareProduct = $gridcontext->getCompareProduct();
        $this->wishlistHelper = $gridcontext->getWishlistHelper();
        $this->row = $row;
        $this->helperData = $helperData;
        $this->productLabelViewModel = $productLabelViewModel;
        $this->authenticationviewmodel = $authenticationviewmodel;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * Check if the module has been enabled in the admin
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BESTSELLER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if the module has been enabled in the admin
     *
     * @return bool
     */
    public function rowProduct()
    {
        return $this->scopeConfig->getValue(
            self::XML_PRODUCT_ROW,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return \Magento\Catalog\Helper\Product\Compare
     * @since 101.0.1
     */
    public function getCompareHelper()
    {
        return $this->compareProduct;
    }
    /**
     * return best seller products
     *
     * @return mixed $productCollection
     **/
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * return best seller products
     *
     * @return mixed $productCollection
     **/
    protected function _getProductCollection()
    {
        try {
            if ($this->_productCollection === null) {
                $this->_productCollection = $this->initializeProductCollection();
            }
            return $this->_productCollection;
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }
        return false;
    }

    /**
     * return best seller products
     *
     * @return mixed $productCollection
     **/
    private function initializeProductCollection()
    {
        try {
            $selectid = $this->rowProduct();
            $productIds = [];
            $bestSellers = $this->bestSellersCollectionFactory->create()
                ->setPeriod('month');
            foreach ($bestSellers as $product) {
                $productIds[] = $product->getProductId();
            }
            $collection = $this->productCollectionFactory->create()->addIdFilter($productIds);
            $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('*')
                ->addStoreFilter($this->getStoreId())
                ->setPageSize($selectid);
            return $collection;
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }
        return false;
    }

    public function getAddToWishlistParams($product)
    {
        return $this->wishlistHelper->getAddParams($product);
    }
    public function getAddToCompareUrl()
    {
        return $this->compareProduct->getAddUrl();
    }
    
    public function productAttribute($product, $attributeHtml, $attributeName)
    {
        try {
            $attribute = $this->eavConfig->getAttribute(ModelProduct::ENTITY, $attributeName);
            if ($attribute &&
                $attribute->getId() &&
                $attribute->getFrontendInput() !== 'media_image' &&
                (!$attribute->getIsHtmlAllowedOnFront() &&
                !$attribute->getIsWysiwygEnabled())
            ) {
                if ($attribute->getFrontendInput() !== 'price') {
                    $attributeHtml = $this->escaper->escapeHtml($attributeHtml);
                }
                if ($attribute->getFrontendInput() === 'textarea') {
                    $attributeHtml = nl2br($attributeHtml);
                }
            }
            if ($attributeHtml !== null
                && $attribute->getIsHtmlAllowedOnFront()
                && $attribute->getIsWysiwygEnabled()
                && $this->isDirectivesExists((string)$attributeHtml)
            ) {
                $attributeHtml = $this->getTemplateProcessor()->filter($attributeHtml);
            }
            return $attributeHtml;
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }
        return false;
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
