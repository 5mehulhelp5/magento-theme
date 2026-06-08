<?php
namespace Forever\Core\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class ZoomInOut extends Template
{
    const ISENABLE = 'themedesign/zoominout/enable';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return Scope Config Value
     */
    public function getConfigData()
    {
        $value = $this->config->getValue(
            self::ISENABLE,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getStoreId()
        );
        return $value;
    }
}
