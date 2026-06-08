<?php

namespace Forever\Megamenu\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $configModule;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        parent::__construct($context);
        $this->moduleManager = $moduleManager;
        $module = strtolower(str_replace('Forever_', '', $this->_getModuleName()));
        $this->configModule = $this->getConfig($module);
    }

    /**
     * Get config
     *
     * @return Config Value
     */
    public function getConfig($cfg = '')
    {
        if ($cfg) {
            return $this->scopeConfig->getValue($cfg, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
        return $this->scopeConfig;
    }

    /**
     * Get config module
     *
     * @return config module
     */
    public function getConfigModule($cfg = '', $value = null)
    {
        $values = $this->configModule;
        if (!$cfg) {
            return $values;
        }
        $config = explode('/', $cfg);
        $end = count($config) - 1;
        foreach ($config as $key => $vl) {
            if (isset($values[$vl])) {
                if ($key == $end) {
                    $value = $values[$vl];
                } else {
                    $values = $values[$vl];
                }
            }
        }
        return $value;
    }

    /**
     * Get Enable Module
     *
     * @return Enable Module
     */
    public function isEnabledModule($moduleName)
    {
        return $this->moduleManager->isEnabled($moduleName);
    }
}
