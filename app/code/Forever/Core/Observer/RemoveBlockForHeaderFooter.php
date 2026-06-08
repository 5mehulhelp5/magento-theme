<?php

namespace Forever\Core\Observer;

use Magento\Framework\Event\ObserverInterface;

class RemoveBlockForHeaderFooter implements ObserverInterface
{
    /**
     * @var \Forever\Core\ViewModel\SystemConfigurations
     */
    protected $coreViewModel;

    /**
     *
     * @param \Forever\Core\ViewModel\SystemConfigurations $coreViewModel
     */
    public function __construct(
        \Forever\Core\ViewModel\SystemConfigurations $coreViewModel
    ) {
        $this->coreViewModel = $coreViewModel;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $layout = $observer->getData('layout');
        $footerstyle = $this->coreViewModel->getfooterStyle();
        if ($footerstyle == 'style-1') {
            $layout->unsetElement('footer-2-footer-top-section');
            $layout->unsetElement('footer-2-footer-middle-section');

            $layout->unsetElement('footer-3-footer-top-section');
            $layout->unsetElement('footer-3-footer-middle-section');
            $layout->unsetElement('footer-3-footer-bottom-section');

            $layout->unsetElement('footer-4-footer-top-section');
            $layout->unsetElement('footer-4-footer-middle-section');
            $layout->unsetElement('footer-4-footer-bottom-section');

        } elseif ($footerstyle == 'style-2') {
            $layout->unsetElement('footer-1-footer-top-section');
            $layout->unsetElement('footer-1-footer-middle-section');
            $layout->unsetElement('footer-1-footer-bottom-section');

            $layout->unsetElement('footer-3-footer-top-section');
            $layout->unsetElement('footer-3-footer-middle-section');
            $layout->unsetElement('footer-3-footer-bottom-section');

            $layout->unsetElement('footer-4-footer-top-section');
            $layout->unsetElement('footer-4-footer-middle-section');
            $layout->unsetElement('footer-4-footer-bottom-section');

        } elseif ($footerstyle == 'style-3') {
            $layout->unsetElement('footer-1-footer-top-section');
            $layout->unsetElement('footer-1-footer-middle-section');
            $layout->unsetElement('footer-1-footer-bottom-section');

            $layout->unsetElement('footer-2-footer-top-section');
            $layout->unsetElement('footer-2-footer-middle-section');

            $layout->unsetElement('footer-4-footer-top-section');
            $layout->unsetElement('footer-4-footer-middle-section');
            $layout->unsetElement('footer-4-footer-bottom-section');
        } elseif ($footerstyle == 'style-4') {
            $layout->unsetElement('footer-1-footer-top-section');
            $layout->unsetElement('footer-1-footer-middle-section');
            $layout->unsetElement('footer-1-footer-bottom-section');

            $layout->unsetElement('footer-2-footer-top-section');
            $layout->unsetElement('footer-2-footer-middle-section');

            $layout->unsetElement('footer-3-footer-top-section');
            $layout->unsetElement('footer-3-footer-middle-section');
            $layout->unsetElement('footer-3-footer-bottom-section');
        }
    }
}
