<?php

namespace Forever\Blog\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class BlogData implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    const XML_PATH_EMAIL_RECIPIENT = 'blog/general/enable';

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeScope;

    /**
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeScope
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeScope
    ) {
        $this->storeScope = $storeScope;
        $this->scopeConfig = $scopeConfig;
        
    }

    /**
     * @return getConfig
     */
    public function getConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope);
    }

    /**
     * @return getMediaUrl
     */
    public function getMediaUrl()
    {
        $media_dir = $this->storeScope->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $media_dir;
    }
}
