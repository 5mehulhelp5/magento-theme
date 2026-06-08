<?php

namespace Forever\AuthenticationPopUp\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Forever\AuthenticationPopUp\Helper\Data as AjaxLoginHelper;

class AuthPopupObserver implements ObserverInterface
{
    /**
     * @param Session $customerSession
     * @param AjaxLoginHelper $helper
     */
    public function __construct(
        private readonly Session $customerSession,
        private readonly AjaxLoginHelper $helper
    ) {
    }

    /**
     * Add a custom handle responsible for adding the trigger-ajax-login class
     *
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->helper->isModuleEnabled()) {
            $layout = $observer->getEvent()->getLayout();
            if (!$this->customerSession->isLoggedIn()) {
                $layout->getUpdate()->addHandle('ajaxlogin_customer_logged_out');
            }
        }
    }
}
