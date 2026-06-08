<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Blog\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @Class TagList
 */
class TagList extends Template
{
    /**
     * @var $collectionFactory
     */
    protected $collectionFactory;
    /**
     * @var $scopeConfig
     */
    protected $scopeConfig;
    /**
     * @param Template\Context                                         $context
     * @param \Forever\Blog\Model\ResourceModel\Tag\CollectionFactory $collectionFactory
     * @param array                                                    $data
     */
    public function __construct(
        Template\Context $context,
        \Forever\Blog\Model\ResourceModel\Tag\CollectionFactory $collectionFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storManager,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storManager = $storManager;
    }
    /**
     * @return getTagList
     */
    public function getTagList()
    {

        $collection = $this->collectionFactory->create()
        ->addFieldToSelect('*')
        ->addFieldToFilter('status', ['eq' => '1']);
        return $collection;
    }
    /**
     * @return getTagViewUrl
     */
    public function getTagViewUrl($tagviewUrlKey)
    {
        $createurlKey = strtolower($tagviewUrlKey);
        $createurlKey = str_replace(' ', '-', $createurlKey);
        $baseUrl = $this->storManager->getStore()->getBaseUrl();
        $getTagViewUrl = $baseUrl . 'blog/tag/view/' . $createurlKey;
        return $getTagViewUrl;
    }
}
