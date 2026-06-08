<?php

namespace Forever\AuthenticationPopUp\Plugin;

use Magento\Customer\Block\Account\AuthorizationLink;
use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Forever\AuthenticationPopUp\Helper\Data as AjaxLoginHelper;

class SigninLinkPlugin
{
    /**
     * @param HttpContext $httpContext
     * @param AjaxLoginHelper $helper
     */
    public function __construct(
        private readonly HttpContext $httpContext,
        private readonly AjaxLoginHelper $helper
    ) {
    }

    /**
     * @param AuthorizationLink $subject
     * @param string $result
     * @return string
     */
    public function afterGetHref(AuthorizationLink $subject, string $result): string
    {
        if ($this->helper->isModuleEnabled() && !$this->isLoggedIn()) {
            return '#';
        }

        return $result;
    }

    /**
     * Check if customer is logged in
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return (bool) $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }
}
