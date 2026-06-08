<?php

namespace Forever\Megamenu\Ui\Component\Listing\Megamenu\Column;

use Magento\Backend\Block\Context;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Block extends Column
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockFactory
     */
    protected $blockFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory
     */
    protected $categoryFactory;

    /**
     * @var allblocks
     */
    protected $allBlocks;

    /**
     * @var array
     */
    protected $position = [ 'rightcontent', 'leftcontent'];

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->storeManager = $storeManager;
        $this->bockFactory  = $blockFactory;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Preparte Block
     *
     * @return array
     */
    public function prepareBlocks()
    {
        if (!$this->allBlocks) {
            $demo = $this->bockFactory->create()->toOptionArray();
            foreach ($demo as $value) {
                $this->allBlocks[$value['value']] = $value['label'];
            }
        }
        return $this->allBlocks;
    }

    /**
     * Preparte Datasource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $catecoll = $this->categoryFactory->create()->addAttributeToSelect('*');
        foreach ($catecoll as $catelabel) {
            $allcatename[$catelabel['entity_id']] = $catelabel['name'];
        }
        
        if (isset($dataSource['data']['items'])) {
            $this->prepareBlocks();
            foreach ($dataSource['data']['items'] as & $item) {
                foreach ($this->position as $pos) {
                    if (isset($this->allBlocks[$item[$pos]])) {
                        $item[$pos] = $this->allBlocks[$item[$pos]];
                    }
                }
    
                foreach ($allcatename as $key => $catla) {
                    if ($item['cat_id'] == $key) {
                        $item['cat_id'] = $catla;
                    }
                }
            }
        }
        return $dataSource;
    }
}
