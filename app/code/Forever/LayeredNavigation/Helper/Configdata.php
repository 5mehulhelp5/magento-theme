<?php

namespace Forever\LayeredNavigation\Helper;

class Configdata extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var array
     */
    protected $configModule;

    /**
     * @param \Magento\Framework\App\Helper\Context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->configModule = $this->getConfig(strtolower($this->_getModuleName()));
    }

    /**
     * @param $cfg | bool
     * @return bool
     */
    public function getConfig($cfg = '')
    {
        if ($cfg) {
            return $this->scopeConfig->getValue($cfg, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
            return $this->scopeConfig;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getConfig('layered_navigation/general/enabled');
    }
}
