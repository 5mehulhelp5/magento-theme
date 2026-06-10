<?php

declare(strict_types=1);

namespace Forever\Faq\Block;

use Forever\Faq\Model\ResourceModel\Question\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class Questions extends Template
{
    public const MAIN_LABEL = 'Default';
    public const MODULE_ENABLE = 'faq/general/enable';

    public function __construct(
        Template\Context $context,
        protected readonly CollectionFactory $collectionFactory,
        protected readonly ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get all enabled questions
     *
     * @return DataObject[]
     */
    public function getItems(): array
    {
        $questionCollection = $this->collectionFactory->create();
        $questionCollection->addFieldToFilter('main_table.status', 1);
        return $questionCollection->getItems();
    }

    /**
     * Check if module is enabled
     */
    public function getConfigValue(): mixed
    {
        return $this->scopeConfig->getValue(self::MODULE_ENABLE, ScopeInterface::SCOPE_STORE);
    }
}
