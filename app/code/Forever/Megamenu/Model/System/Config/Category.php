<?php

namespace Forever\Megamenu\Model\System\Config;

class Category implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\Config\Source\Category
     */
    protected $category;

    /**
     * @var \Magento\Framework\App\RequestInterface $request
     */
    protected $request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    protected $options = [];

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\Config\Source\Category $category
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Config\Source\Category $category
    ) {
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
        $this->category = $category;
    }

    /**
     * Return option in array
     *
     * @return array
     */
    public function toOptionArray()
    {

        if (!$this->options) {
            $store = $this->request->getParam('store');
            if (!$store) {
                $categories = $this->category->toOptionArray();
            } else {
                $rootCategoryId = $this->storeManager->getStore($store)->getRootCategoryId();
                $label = $this->categoryFactory->create()->load($rootCategoryId)->getName();
                $categories = [['value' => $rootCategoryId, 'label' => $label]];
            }
            $options = [];
            foreach ($categories as $category) {
                if ($category['value']) {
                    $rootOption = ['label' => $category['label']];
                    $_categories = $this->categoryFactory->create()->getCategories($category['value']);
                    $childOptions = [];
                    foreach ($_categories as $category) {
                        $childOptions[] = [
                            'label' => $category->getName(),
                            'value' => $category->getId()
                        ];
                    }
                    $rootOption['value'] = $childOptions;
                    $options[] = $rootOption;
                }
            }
            $this->options = $options;
        }

        return $this->options;
    }
}
