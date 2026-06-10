<?php

namespace Forever\NewsletterPopup\Block;

use Magento\Store\Model\ScopeInterface;

class Showpopup extends \Magento\Framework\View\Element\Template
{
    const CONFIG_PATH_ENABLE = 'newsletter/general/enable';
    const CONFIG_PATH_SHOW_ONLY_HOMEPAGE = 'newsletter/general/onlyhomepage';
    const CONFIG_PATH_POPUP_HEIGHT = 'newsletter/general/height';
    const CONFIG_PATH_POPUP_WIDTH = 'newsletter/general/width';
    const CONFIG_PATH_POPUP_TITLE = 'newsletter/general/heading';

    /**
     * @var Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->context = $context;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * Get Scope Config
     *
     * @return bool
     */
    public function getConfig($value)
    {
        return $this->scopeConfig->getValue(
            $value,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * return weather module is enable or disable
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PATH_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * return weather popup will show in homepage or all the pages
     *
     * @return bool
     */
    public function showinHomepageOnly()
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PATH_SHOW_ONLY_HOMEPAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * return height of newsletter popup
     *
     * @return bool
     */
    public function getHeight()
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PATH_POPUP_HEIGHT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * return width of newsletter popup
     *
     * @return bool
     */
    public function getWidth()
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PATH_POPUP_WIDTH,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * return title of newsletter popup
     *
     * @return bool
     */
    public function getTitle()
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PATH_POPUP_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
