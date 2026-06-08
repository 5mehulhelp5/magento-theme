<?php

namespace Forever\Team\Block;

use Magento\Framework\View\Element\Template;
use Forever\Team\Model\ResourceModel\Team\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class Team extends Template
{
    /*
    * This label won't be displayed in the frontend block
    */
    const MAIN_LABEL = 'Default';

    const MODULE_ENABLE = 'team/general/enable';

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    protected $storeManager;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Get All Questions
     *
     * @return \Magento\Framework\DataObject[]
     */
    public function getItems()
    {
        $questionCollection = $this->collectionFactory->create();
        $questionCollection->addFieldToFilter('main_table.status', 1);

        return $questionCollection->getItems();
    }
    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    public function getConfigValue()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::MODULE_ENABLE, $storeScope);
    }
}
