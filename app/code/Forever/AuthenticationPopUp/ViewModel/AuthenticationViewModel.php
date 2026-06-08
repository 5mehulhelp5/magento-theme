<?php

namespace Forever\AuthenticationPopUp\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Forever\AuthenticationPopUp\Helper\Data as AjaxLoginHelper;

class AuthenticationViewModel implements ArgumentInterface
{
    /**
     * @param AjaxLoginHelper $helper
     */
    public function __construct(
        private readonly AjaxLoginHelper $helper
    ) {
    }

    /**
     * Check if module is enabled via config
     *
     * @return bool
     */
    public function getScopeconfig(): bool
    {
        return $this->helper->isModuleEnabled();
    }
}
