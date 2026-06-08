<?php

declare(strict_types=1);

namespace Forever\Map\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class DataConfig implements ArgumentInterface
{
    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfigValue(string $path, ?string $scopeCode = null): mixed
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $scopeCode);
    }

    public function isEnabled(): bool
    {
        return (bool)(int)$this->getConfigValue('fgooglemaps/general/enabled');
    }
}
