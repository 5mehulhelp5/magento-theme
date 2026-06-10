<?php

declare(strict_types=1);

namespace Forever\Faq\Block\Adminhtml\Question\Buttons;

use Magento\Backend\Block\Widget\Context;

class GenericButton
{
    protected readonly \Magento\Framework\UrlInterface $urlBuilder;

    public function __construct(
        protected readonly Context $context
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
    }

    /**
     * Return the entity Id from request param
     */
    public function getId(): ?int
    {
        $id = $this->context->getRequest()->getParam('id');
        return $id !== null ? (int) $id : null;
    }

    /**
     * Generate url by route and parameters
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
