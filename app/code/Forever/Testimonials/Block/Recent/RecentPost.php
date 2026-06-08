<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Testimonials\Block\Recent;

use Magento\Framework\View\Element\Template;
use Forever\Testimonials\Model\ResourceModel\Testimonials\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Catalog\Helper\ImageFactory;

/**
 * @Class MostView
 * package forever\Testimonials\Block\Sidebar
 */
class RecentPost extends Template
{
    /**
     * @return helperImageFactory
     */
    public $helperImageFactory;
    
    /**
     * @return assetRepos
     */
    public $assetRepos;

    /**
     * @return scopeConfig
     */
    protected $scopeConfig;
    /**
     * @return blogFactory
     */
    // protected $blogFactory;
    /**
     * @return storManager
     */
    protected $storManager;
    
    const RECENT_POST = 'testimonials/recent_testimonials/number_recent_posts';
    /**
     *
     * @param Template\Context      $context
     * @param CollectionFactory     $collectionFactory
     * @param TestimonialsFactory           $blogFactory
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storManager
     * @param Repository $assetRepos
     * @param ImageFactory $helperImageFactory
     * @param array                 $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storManager,
        Repository $assetRepos,
        ImageFactory $helperImageFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_isScopePrivate = true;
        $this->storManager = $storManager;
        $this->assetRepos = $assetRepos;
        $this->helperImageFactory = $helperImageFactory;

        parent::__construct($context, $data);
    }
    /**
     * @return getConfig value
     */
    public function getConfigData($path)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue($path, $storeScope);
    }
    /**
     * @return Testimonials Collection
     */
    public function testimonialsCollection()
    {
        $testimonialsCollection = $this->collectionFactory->create()        
        ->addFieldToSelect('*')
        ->addFieldToFilter('status', ['eq' => '1']);
        return $testimonialsCollection;
    }
    /**
     * @return Recent added Collection
     */
    public function getRecentPost()
    {
        $collection = $this->testimonialsCollection()->setOrder('created_at', 'DESC');
        $collection->getSelect()
            ->limit((int)$this->getConfigData(self::RECENT_POST) ?: 2);
        return $collection;
    }
    /**
     * @return MediaUrl
     */
    public function getMediaUrl()
    {
        $media_dir = $this->storManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $media_dir;
    }
    
    public function getViewUrl()
    {
        $baseUrl = $this->storManager->getStore()->getBaseUrl();
        $getViewUrl = $baseUrl . 'testimonials/';
        return $getViewUrl;
    }

    public function getPlaceHolderImage()
    {
        $imagePlaceholder = $this->helperImageFactory->create();
        return $this->assetRepos->getUrl($imagePlaceholder->getPlaceholder('small_image'));
    }
}
