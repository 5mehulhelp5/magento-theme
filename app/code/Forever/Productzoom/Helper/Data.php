<?php

declare(strict_types=1);

namespace Forever\Productzoom\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * @var array|null
     */
    protected ?array $configModule = null;

    public function __construct(Context $context)
    {
        parent::__construct($context);
        // Module name lowercased = forever_productzoom
        $this->configModule = $this->getConfig('forever_productzoom');
    }

    /**
     * Get raw scope config value or the scopeConfig object
     */
    public function getConfig(string $cfg = ''): mixed
    {
        if ($cfg) {
            return $this->scopeConfig->getValue($cfg, ScopeInterface::SCOPE_STORE);
        }
        return $this->scopeConfig;
    }

    /**
     * Get nested config value by slash-delimited path
     */
    public function getConfigModule(string $cfg = '', mixed $value = null): mixed
    {
        $values = $this->configModule;
        if (!$cfg) {
            return $values;
        }
        $config = explode('/', $cfg);
        $end    = count($config) - 1;
        foreach ($config as $key => $vl) {
            if (isset($values[$vl])) {
                if ($key === $end) {
                    $value = $values[$vl];
                } else {
                    $values = $values[$vl];
                }
            }
        }
        return $value;
    }
}
