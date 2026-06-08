<?php

namespace Forever\Core\Observer;

use Magento\Store\Model\ScopeInterface;

class LayoutLoadBefore implements \Magento\Framework\Event\ObserverInterface
{
    const XML_PATH_HEADER_STYLE = 'forever_general/header/style';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param Magento\Framework\Registry $_registry
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_registry = $registry;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $headerType = $this->getHeaderStyle();
        $layout = $observer->getLayout();
        try {
            if ($headerType) {
                $layout->getUpdate()->addHandle($headerType);
            }
        } catch (Exception $e) {
            $layout->getUpdate()->addHandle('default_header');
        }
        return $this;
    }

    public function getHeaderStyle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HEADER_STYLE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
