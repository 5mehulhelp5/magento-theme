<?php

namespace Forever\Megamenu\Block\Adminhtml\Helper\Renderer\Grid;

class Categorys implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    protected $categories;
    
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collection,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->categories = $collection;
        $this->storeManager = $storeManager;
    }

    /**
     * Return category
     *
     * @return array
     */
    public function toOptionArray()
    {
        $categories = $this->categories->create();
        $collection = $categories->addAttributeToSelect('*')
        ->addFieldToFilter('is_active', 1)->setStore($this->storeManager->getStore());
        $itemArray = ['value' => '', 'label' => '--Please Select--'];
        $categoryArray = [];
        $categoryArray[] = $itemArray;
        foreach ($collection as $category) {
            if ($category->getLevel() == '2') {
                $categoryArray[] = ['label' => $category->getName(),'value' => $category->getId(),];
            }
        }
        return $categoryArray;
    }
}
