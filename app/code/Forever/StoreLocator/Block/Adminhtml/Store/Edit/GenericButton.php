<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\StoreLocator\Block\Adminhtml\Store\Edit;

use Magento\Backend\Block\Widget\Context;

/**
 * Generic button provider - base class for edit form buttons.
 */
class GenericButton
{
    /**
     * @var Context
     */
    private Context $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Return the store_id request param (null for new record).
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        $id = $this->context->getRequest()->getParam('store_id');
        return $id ? (int) $id : null;
    }

    /**
     * Generate URL by route and parameters.
     *
     * @param  string $route
     * @param  array  $params
     * @return string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
