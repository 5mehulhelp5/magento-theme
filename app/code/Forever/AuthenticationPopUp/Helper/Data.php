<?php

namespace Forever\AuthenticationPopUp\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * System config path
     */
    public const AJAXLOGIN_POPUP_XML_PATH = 'popup/general/ajaxlogin_enable';

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Check if the module has been enabled in the admin
     *
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::AJAXLOGIN_POPUP_XML_PATH,
            ScopeInterface::SCOPE_STORE
        );
    }
}
