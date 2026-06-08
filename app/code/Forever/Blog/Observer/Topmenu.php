<?php

namespace Forever\Blog\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\ObserverInterface;

class Topmenu implements ObserverInterface
{
    const XML_PATH_EMAIL_RECIPIENT = 'blog/general/enable';

    /**
     * @var $scopeConfig
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {

        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(EventObserver $observer)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $configValue = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope);
        if ($configValue == 1) {
            $menu = $observer->getMenu();
            $tree = $menu->getTree();
            $data = ['name' => __('Blog'),
                'id' => 'blog',
                'url' => $this->urlBuilder->getUrl('blog/'),
                'is_active' => false];
            $node = new Node($data, 'id', $tree, $menu);
            $menu->addChild($node);
            return $this;
        }
    }
}
