<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\StoreLocator\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Topmenu implements ObserverInterface
{
    const MODULE_ENABLE = 'storelocator/general/enable';

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param UrlInterface         $urlBuilder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->urlBuilder  = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Add "Find a store" link to the top menu when module is enabled.
     *
     * @param  EventObserver $observer
     * @return $this|void
     */
    public function execute(EventObserver $observer)
    {
        $configValue = $this->scopeConfig->getValue(self::MODULE_ENABLE, ScopeInterface::SCOPE_STORE);

        if ((int) $configValue !== 1) {
            return $this;
        }

        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        $data = [
            'name'      => __('Find a store'),
            'id'        => 'find-a-store',
            'url'       => $this->urlBuilder->getUrl('storelocator/'),
            'is_active' => false,
        ];

        $node = new Node($data, 'id', $tree, $menu);
        $menu->addChild($node);

        return $this;
    }
}
