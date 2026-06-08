<?php

namespace Forever\InfiniteScroll\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class InfiniteScroll extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $config
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $config,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * Get media base URL, optionally with image path appended.
     *
     * @param string|null $img
     * @return string
     */
    public function getMedia(?string $img = null): string
    {
        $urlMedia = '';
        try {
            $urlMedia = $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );
        } catch (\Exception $e) {
            // Use the logger available from AbstractBlock via Context — safe in all PHP versions
            $this->_logger->error($e->getMessage());
        }

        return $img ? $urlMedia . $img : $urlMedia;
    }

    /**
     * Get store config value by path.
     *
     * @param string $path
     * @return mixed
     */
    public function getConfigData(string $path): mixed
    {
        return $this->config->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getStoreId()
        );
    }
}
