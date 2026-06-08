<?php

namespace Forever\Megamenu\Block;

use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\Node\Collection;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;

class Menu extends \Magento\Catalog\Block\Navigation
{

    const DEFAULT_CACHE_TAG = 'FOREVER_MEGAMENU';

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * Top menu data tree
     *
     * @var Node
     */
    protected $_menu;

    /**
     * @var NodeFactory
     */
    private $nodeFactory;

    /**
     * @var TreeFactory
     */
    private $treeFactory;

    /**
     * magicmenu collection factory.
     *
     * @var \Forever\Megamenu\Model\ResourceModel\Magicmenu\CollectionFactory
     */
    protected $_magicmenuCollectionFactory;

    /**
     * @var urmedia
     */
    protected $_urlMedia;

    /**
     * @var dimedia
     */
    protected $_dirMedia;

    /**
     * @var extdata
     */
    protected $extData = [];

    /**
     * @var syscfg
     */
    public $_sysCfg;
    
    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $fileDriver;
    
    /**
     * @var \Forever\Megamenu\Helper\Data
     */
    public $_helper;

    /**
     * @var rootcategory
     */
    public $rootCategory;

    /**
     * @var filterprovider
     */
    protected $filterProvider;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Catalog\Helper\Category $catalogCategory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $flatState
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Forever\Megamenu\Helper\Data $helper
     * @param \Forever\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory $magicmenuCollectionFactory
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $flatState,
        \Forever\Megamenu\Helper\Data $helper,
        \Forever\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory $magicmenuCollectionFactory,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        ?\Magento\Framework\Serialize\Serializer\Json $serializer = null,
        array $data = []
    ) {

        $this->_helper = $helper;
        $this->_magicmenuCollectionFactory = $magicmenuCollectionFactory;
        $this->_sysCfg = (object) $this->_helper->getConfigModule();
        $this->filterProvider = $filterProvider;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
        ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        $this->fileDriver = $fileDriver;
        $this->nodeFactory = $nodeFactory;
        $this->treeFactory = $treeFactory;

        parent::__construct(
            $context,
            $categoryFactory,
            $productCollectionFactory,
            $layerResolver,
            $httpContext,
            $catalogCategory,
            $registry,
            $flatState,
            $data
        );

        $this->_urlMedia = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );

        $this->_dirMedia = $this->getMediaDirectory()->getAbsolutePath();
    }

    /**
     * Get cache life time
     *
     * @return cachelifetime
     */
    protected function getCacheLifetime()
    {
        return parent::getCacheLifetime() ?: 86400;
    }

    /**
     * Get cache key info
     *
     * @return Cachekeyinfo
     */
    public function getCacheKeyInfo()
    {
        $keyInfo     =  parent::getCacheKeyInfo();
        $keyInfo[]   =  $this->getCurrentCategory()->getId();
        return $keyInfo;
    }

    /**
     * get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::DEFAULT_CACHE_TAG, self::DEFAULT_CACHE_TAG . '_' . $this->getCurrentCategory()->getId()];
    }

    /**
     * Get home
     *
     * @return Home Page
     */
    public function getIsHomePage()
    {
        return $this->getUrl('') == $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
    }

    /**
     * is active category
     *
     * @return Active Category
     */
    public function isCategoryActive($catId)
    {
        return $this->getCurrentCategory() ? in_array($catId, $this->getCurrentCategory()->getPathIds()) : false;
    }

    /**
     * Get has category
     *
     * @return Has Category
     */
    public function isHasActive($catId)
    {
        return ($catId != $this->getCurrentCategory()->getId()) ? true : false;
    }

    /**
     * Get active class
     *
     * @return Active Class
     */
    protected function _getActiveClasses($catId)
    {
        $classes = '';
        if ($this->isCategoryActive($catId)) {
            if ($this->isHasActive($catId)) {
                $classes = 'has-active active';
            } else {
                $classes = 'active';
            }
        }
        return $classes;
    }

    /**
     * Get logo
     *
     * @return Logo
     */
    public function getLogo()
    {
        return $this->getLayout()->createBlock(\Magento\Theme\Block\Html\Header\Logo::class)->toHtml();
    }

    /**
     * Get root category
     *
     * @return Root Category
     */
    public function getRootCategory()
    {
        if (!$this->rootCategory) {
            $rootCatId = $this->_storeManager->getStore()->getRootCategoryId();
            $this->rootCategory = $this->_categoryInstance->load($rootCatId);
        }
        return $this->rootCategory;
    }

    /**
     * Get root name
     *
     * @return Root Category Name
     */
    public function getRootName()
    {
        return $this->getRootCategory()->getName();
    }

    /**
     * Draw Home Menu
     *
     * @return Menu
     */
    public function drawHomeMenu()
    {
        /*if ($this->hasData('homeMenu')) {
            return $this->getData('homeMenu');
        }*/
        $drawHomeMenu = '';
        $active = ($this->getIsHomePage()) ? ' active' : '';
        $drawHomeMenu .= '<li class="level0 category-item level-top dropdown home' . $active . '">';
        $drawHomeMenu .= '<a class="level-top" href="' . $this->getBaseUrl() . '">
        <span class="icon fa fa-home"></span><span class="icon-text">' . __('Home') . '</span>';
        $drawHomeMenu .= '</a>';

        if ($this->_sysCfg->module['enabled']) {
            $demo = '';
            $currentStore = $this->_storeManager->getStore();
            $switcher = $this->getLayout()->createBlock(\Magento\Store\Block\Switcher::class);
            $data=$this->getGroup();

            foreach ($this->_storeManager->getWebsites() as $website) {
                $groups = $website->getGroups();
                if (count($groups) > 1) {
                    foreach ($groups as $group) {
                        $demo=$this->drawHomestructure($group);
                    }
                }
            }
            if ($demo) {
                $drawHomeMenu .= '<ul class="level0 category-item submenu">' . $demo . '</ul>';
            }
        }

        $drawHomeMenu .= '</li>';
        $this->setData('homeMenu', $drawHomeMenu);
        return $drawHomeMenu;
    }

    public function drawHomestructure($group)
    {
        $currentStore = $this->_storeManager->getStore();
        $switcher = $this->getLayout()->createBlock(\Magento\Store\Block\Switcher::class);
        $data=$this->getGroup();
        $demo = '';
        $store = $group->getDefaultStore();
        if ($store && !$store->getIsActive()) {
            $stores = $group->getStores();
            foreach ($stores as $store) {
                if ($store->getIsActive()) {
                    break;
                } else {
                    $store = '';
                }
            }
        }
        if ($store) {
            if ($store->getCode() == $currentStore->getCode()) {
                return $demo .= '<li class="level1">
                <a href="' . $store->getBaseUrl() . '">
                <span class="demo-home">' . $group->getName() . '</span></a></li>';
            } else {
                $dataPost = $switcher->getTargetStorePostData($store);
                $dataPost = $this->serializer->unserialize($dataPost);
                if (isset($dataPost['action']) && isset($dataPost['data'])) {
                    $href = $dataPost['action'] . '?' . http_build_query($dataPost['data']);
                    return  $demo .= '<li class="switcher-option level1">
                    <a href="' . $href . '">
                    <span class="demo-home">' . $group->getName() . '</span></a></li>';
                }
            }
        }
    }

    /**
     * Draw Main menu
     *
     * @return Main Menu
     */
    public function drawMainMenu()
    {
        if ($this->hasData('mainMenu')) {
            return $this->getData('mainMenu');
        }

        $desktopHtml   = [];
        $mobileHtml    = [];
        $rootCategory  = $this->getRootCategory();
        $storeId       = $this->_storeManager->getStore()->getId();
        $rootCatId     = $rootCategory->getId();
        $categories    = $this->getTreeMenu($storeId, $rootCatId);
        $contentCatTop = $this->getContentCatTop();

        foreach ($contentCatTop as $ext) {
            $this->extData[$ext->getCatId()] = $ext->getData();
        }
        $test=$this->_magicmenuCollectionFactory->create();
    
        $last = count($categories);
        $counter = 1;
        $this->removeChildrenWithoutActiveParent($categories, 0);
        $catIcons = [];
        $catIds = $this->_magicmenuCollectionFactory->create()
        ->addFieldToFilter('status', 1)
        ->addFieldToSelect('cat_id')->getColumnValues('cat_id');
        if (is_array($catIds) && count($catIds)) {
            // $catIds = array_unique($catIds);
            $catIds = $catIds;
        }
        foreach ($categories as $catTop) {
            $catIcons[] = $catTop->getCategoryicon();
            if (!in_array($catTop->getEntityId(), $catIds) || !$catTop->getData('is_parent_active')) {
                continue;
            }
            $parentPositionClass = '';
            $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';
            $idTop = $catTop->getEntityId();
            if ($catTop->getCategoryicon()) {
                $categoryImage = '<span class="categoryicon">
                <img class="customimageicon" src="' . $this->getBaseUrl() . $catTop->getCategoryicon() . '"/></span>';
            } else {
                $categoryImage = '';
            }

            $urlTop =  '<a class="level-top" href="' . $catTop->getUrl() . '">'
            . $this->getThumbnail($catTop) . $categoryImage . '<span>'
            . $catTop->getName() . '</span><span class="boder-menu"></span></a>';

            $itemPositionClassPrefixTop = $itemPositionClassPrefix . $counter;
            $classTop   = $itemPositionClassPrefixTop . ' ' . $this->_getActiveClasses($idTop);
            $options  = '';
                $idTop    = $catTop->getEntityId();
                $data     = isset($this->extData[$idTop]) ? $this->extData[$idTop] : '';
                $blocks   = ['leftcontent' => '', 'rightcontent' => ''];
            if ($data) {
                foreach ($blocks as $key => $value) {
                    $proportion = $key . '_proportion';
                    if(!isset($data[$key])){
                        $html = '';
                    } else {
                        $html = $this->filterProvider->getBlockFilter()->filter($data[$key]);
                    }
                    if ($html) {
                        $blocks[$key] = "<div class='mage-column mega-block-$key'>" . $html . '</div>';
                    }
                }

                    $remove = ['leftcontent' => '', 'rightcontent' => '', 'cat_id' => ''];
                foreach ($remove as $key => $value) {
                    unset($data[$key]);
                }
                    $opt     = $this->serializer->serialize($data);
                    $options = $opt ? " data-options='$opt'" : '';
            }
                $menu = $this->getMegamenu($catTop, $blocks, $itemPositionClassPrefixTop);

            if ($menu['desktop']):
                $classTop .= ' hasChild parent';
            endif;
            $desktopHtml[$idTop] = '<li class="level0 category-item level-top cat '
            . $classTop . '"' . $options . '>' . $urlTop . $menu['desktop'] . '</li>';
            $mobileHtml[$idTop]  = '<li class="level0 category-item level-top cat '
            . $classTop . '">' . $urlTop . $menu['mobile'] . '</li>';
            $counter++;
        }
        $menu['desktop'] = $desktopHtml;
        $menu['mobile'] = implode("\n", $mobileHtml);
        $this->setData('mainMenu', $menu);

        return $menu;
    }

    /**
     * Get Mega Menu
     *
     * @return Menu
     */
    public function getMegamenu($catTop, $blocks, $itemPositionClassPrefix)
    {
        // Draw Mega Menu
        $idTop    = $catTop->getEntityId();
        $hasChild = $catTop->hasChildren();
        $desktopTmp = $mobileTmp  = '';
        if ($hasChild || $blocks['leftcontent'] || $blocks['rightcontent']):
            $desktopTmp .= '<div class="level-top-mega">';  /* Wrap Mega */
                $desktopTmp .= '<div class="content-mega">';  /*  Content Mega */
                    $desktopTmp .= '<div class="content-mega-horizontal">';
                        $desktopTmp .= $blocks['leftcontent'];
            if ($hasChild):
                            $desktopTmp .= '<ul class="level0 category-item mage-column cat-mega">';
                            $mobileTmp .= '<ul class="submenu">';
                            $childTop  =  $catTop->getChildren();
                            $childLevel = $this->getChildLevel($catTop->getLevel());
                            $this->removeChildrenWithoutActiveParent($childTop, $childLevel);
                            $counter = 1;
                foreach ($childTop as $cat) {
                    if (!$cat->getData('is_parent_active')):
                        continue;
                    endif;
                    $itemPositionClassPrefixChild = $itemPositionClassPrefix . '-' . $counter;
                    $class = 'level1 category-item '
                    . $itemPositionClassPrefixChild . ' ' . $this->_getActiveClasses($cat->getEntityId());
                    $url =  '<a href="' . $cat->getUrl()
                    . '"><span>' . $cat->getName() . '</span></a>';
                    $catChild  = $cat->getChildren();
                    $childHtml = $this->getTreeCategories(
                        $catChild,
                        $itemPositionClassPrefixChild
                    ); // include magic_label and Maximal Depth
                    $desktopTmp .= '<li class="children ' . $class . '">'
                    . $this->getImage($cat) . $url . $childHtml . '</li>';
                    $mobileTmp  .= '<li class="' . $class . '">' . $url . $childHtml . '</li>';
                    $counter++;
                };
                            $desktopTmp .= '</ul>'; // end cat-mega
                            $mobileTmp .= '</ul>';
            endif;
                        $desktopTmp .= $blocks['rightcontent'];
                    $desktopTmp .= '</div>';
                $desktopTmp .= '</div>';  /* End Content mega */
            $desktopTmp .= '</div>';  /* Warp Mega */
        endif;
        return ['desktop' => $desktopTmp, 'mobile' => $mobileTmp];
    }

    /**
     * Draw Extra Menu
     *
     * @return Extra Menu
     */
    public function drawExtraMenu()
    {
        if ($this->hasData('extraMenu')) {
            return $this->getData('extraMenu');
        }
        $extMenu    = $this->getExtraMenu();
        $count = count($extMenu);
        $drawExtraMenu = '';
        if ($count) {
            $i = 1;
            $class = 'first';
            $currentUrl = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
            foreach ($extMenu as $ext) {
                if ($ext->getCustcateurl() && $ext->getCatename()) {
                    $link = $ext->getCustcateurl();
                    $url = (filter_var($link, FILTER_VALIDATE_URL)) ? $link : $this->getUrl('', ['_direct' => $link]);
                    $active = ( $link && $url == $currentUrl) ? ' active' : '';
                    if ($ext->getImage()) {
                        $categoryImage = '<span class="categoryicon">
                        <img class="customimageicon" 
                        src="' . $this->_urlMedia .
                        'forever/customcategoryimg/image/' .  $ext->getImage() . '" /></span>';
                    } else {
                        $categoryImage = '';
                    }
                    $active .= ' hasChild parent';
                    $drawExtraMenu .= "<li class='level0 category-item level-top ext $class'>";
                    if ($link && $ext->getCatename()):
                            $drawExtraMenu .= '<a class="level-top" href="' . $url . '">' . $categoryImage
                            . '<span>' . $ext->getCatename() . '</span></a>';
                    else:
                            $drawExtraMenu .= '<span class="level-top">'
                            . $categoryImage . '<span>' . $ext->getCatename() . '</span></span>';
                    endif;
                        $drawExtraMenu;
                        $drawExtraMenu .= '</li>';
                        $i++;
                        $class = ($i == $count) ? 'last' : '';
                }
            }
        }
        $this->setData('extraMenu', $drawExtraMenu);
        return $drawExtraMenu;
    }

    /**
     * Get Tree Menu
     *
     * @return Menu
     */
    public function getTreeMenu($storeId, $rootId)
    {
        $collection = $this->getCategoryTree($storeId, $rootId);
        $currentCategory = $this->getCurrentCategory();
        $mapping = [$rootId => $this->getMenu()];  // use nodes stack to avoid recursion
        foreach ($collection as $category) {
            $categoryParentId = $category->getParentId();
            if (!isset($mapping[$categoryParentId])) {
                $parentIds = $category->getParentIds();
                foreach ($parentIds as $parentId) {
                    if (isset($mapping[$parentId])) {
                        $categoryParentId = $parentId;
                    }
                }
            }

            /** @var Node $parentCategoryNode */
            $parentCategoryNode = $mapping[$categoryParentId];

            $categoryNode = new Node(
                $this->getCategoryAsArray(
                    $category,
                    $currentCategory,
                    $category->getParentId() == $categoryParentId
                ),
                'id',
                $parentCategoryNode->getTree(),
                $parentCategoryNode
            );
            $parentCategoryNode->addChild($categoryNode);

            $mapping[$category->getId()] = $categoryNode; //add node in stack
        }
        $menu = isset($mapping[$rootId]) ? $mapping[$rootId]->getChildren() : [];
        return $menu;
    }

    /**
     * Get menu object.
     *
     * Creates Tree root node object.
     * The creation logic was moved from class constructor into separate method.
     *
     * @return Node
     * @since 100.1.0
     */
    public function getMenu()
    {
        if (!$this->_menu) {
            $this->_menu = $this->nodeFactory->create(
                [
                    'data' => [],
                    'idField' => 'root',
                    'tree' => $this->treeFactory->create()
                ]
            );
        }
        return $this->_menu;
    }

    /**
     * Get category tree
     *
     * @return Category Tree
     */
    protected function getCategoryTree($storeId, $rootId)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->_categoryInstance->getCollection();
        $collection->setStoreId($storeId);
        $collection->addAttributeToSelect(['name','categoryicon']);
        $collection->addFieldToFilter('path', ['like' => '1/' . $rootId . '/%']); //load only from store root
        $collection->addAttributeToFilter('include_in_menu', 1);
        $collection->addIsActiveFilter();
        $collection->addNavigationMaxDepthFilter();
        $collection->addUrlRewriteToResult();
        $collection->addOrder('level', 'ASC');
        $collection->addOrder('position', 'ASC');
        $collection->addOrder('parent_id', 'ASC');
        $collection->addOrder('entity_id', 'ASC');
        return $collection;
    }

    /**
     * Convert category to array
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Catalog\Model\Category $currentCategory
     * @param bool $isParentActive
     * @return array
     */
    private function getCategoryAsArray($category, $currentCategory, $isParentActive)
    {

        $categoryId = $category->getId();
        return [
            'name' => $category->getName(),
            'id' => 'category-node-' . $categoryId,
            'url' => $this->_catalogCategory->getCategoryUrl($category),
            'has_active' => in_array((string)$categoryId, explode('/', (string)$currentCategory->getPath()), true),
            'is_active' => $categoryId == $currentCategory->getId(),
            'is_category' => true,
            'is_parent_active' => $isParentActive,
            'entity_id' => $categoryId,
            'level' => $category->getLevel(),
            'categoryicon' => $category->getCategoryicon()
        ];
    }

    /**
     * Remove children from collection when the parent is not active
     *
     * @param Collection $children
     * @param int $childLevel
     * @return void
     */
    private function removeChildrenWithoutActiveParent(Collection $children, int $childLevel): void
    {
        /** @var Node $child */
        foreach ($children as $child) {
            if ($childLevel === 0 && $child->getData('is_parent_active') === false) {
                $children->delete($child);
            }
        }
    }

    /**
     * Retrieve child level based on parent level
     *
     * @param int $parentLevel
     *
     * @return int
     */
    private function getChildLevel($parentLevel): int
    {
        return $parentLevel === null ? 0 : $parentLevel + 1;
    }

    /**
     * Get extra menu
     *
     * @return Extra Menu
     */
    public function getExtraMenu()
    {
        $store = $this->_storeManager->getStore()->getStoreId();
        $collection = $this->_magicmenuCollectionFactory->create()
                        ->addFieldToSelect(['linktype','custcateurl','catename', 'image'])
                        ->addFieldToFilter('linktype', 0)
                        ->addFieldToFilter('status', 1);
        $collection->getSelect()->where('find_in_set(0, stores) OR find_in_set(?, stores)', $store);
        return $collection;
    }

    /**
     * Get static block
     *
     * @return Block
     */
    public function getStaticBlock($id)
    {
        return $this->getLayout()->createBlock(\Magento\Cms\Block\Block::class)->setBlockId($id)->toHtml();
    }

    /**
     * Get content category top
     *
     * @return Category Content
     */
    public function getContentCatTop()
    {
        $store = $this->_storeManager->getStore()->getStoreId();
        $collection = $this->_magicmenuCollectionFactory->create()
                        ->addFieldToSelect([
                                'cat_id','linktype','catename','rightcontent','leftcontent',
                            ])
                        ->addFieldToFilter('stores', [ ['finset' => 0], ['finset' => $store]])
                        ->addFieldToFilter('linktype', 1)
                        ->addFieldToFilter('status', 1);
        return $collection;
    }

    /**
     * Get tree category
     *
     * @return Category
     */
    public function getTreeCategories(
        $categories,
        $itemPositionClassPrefix,
        $count = ''
    ) {
        // include Magic_Label and Maximal Depth
        $html = '';
        $counter = 1;
        foreach ($categories as $category) {
            if (!$category->getData('is_parent_active')):
                continue;
            endif;
            if ($count):
                $cat = $this->_categoryInstance->load($category->getEntityId());
                $count = $count ? '(' . $cat->getProductCount() . ')' : '';
            endif;
            $level = $category->getLevel();
            $catChild  = $category->getChildren();
            $childLevel = $this->getChildLevel($level);
            $this->removeChildrenWithoutActiveParent($catChild, $childLevel);
            $childHtml   = $this->getTreeCategories($catChild, $itemPositionClassPrefix);
            $childClass  = $childHtml ? ' hasChild parent ' : ' ';
            $childClass .= $itemPositionClassPrefix . '-' . $counter;
            $childClass .= ' category-item ' . $this->_getActiveClasses($category->getEntityId());
            $html .= '<li class="level' . ($level - 2) . $childClass . '">
            <a href="' . $category->getUrl() . '">
            <span>' . $category->getName() . $count . "</span></a>\n";
            $html .= $childHtml;
            $html .= '</li>';
            $counter++;
        }
        if ($html):
            $html = '<ul class="level' . ($level - 3) . ' submenu">' . $html . '</ul>';
            return  $html;
        endif;
    }

    /**
     * Get image
     *
     * @return image
     */
    public function getImage($category)
    {
        $url = '';
        $image = $this->_dirMedia . 'forever/megamenu/images/' . $category->getEntityId() . '.png';
        if ($this->fileDriver->isExists($image)):
            $url = $this->_urlMedia . 'forever/megamenu/images/' . $category->getEntityId() . '.png';
        endif;
        if ($url):
            return '<a class="a-image" href="' . $category->getUrl() . '">
            <img class="img-responsive" alt="' . $category->getName() . '" src="' . $url . '"></a>';
        endif;
    }

    /**
     * Get thumbnail
     *
     * @return Category
     */
    public function getThumbnail($category)
    {
        $url = '';
        $image = $this->_dirMedia . 'forever/megamenu/thumbnail/' . $category->getEntityId() . '.png';
        if ($this->fileDriver->isExists($image)):
            $url = $this->_urlMedia . 'forever/megamenu/thumbnail/' . $category->getEntityId() . '.png';
        endif;
        if ($url):
            return '<img class="img-responsive" alt="' . $category->getName() . '" src="' . $url . '">';
        endif;
    }
}
