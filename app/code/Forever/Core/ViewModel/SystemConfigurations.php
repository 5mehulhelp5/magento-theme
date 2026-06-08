<?php
namespace Forever\Core\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class SystemConfigurations implements ArgumentInterface
{
    const XML_PATH_HEADER_STYLE = 'forever_general/header/style';
    const XML_PATH_FOOTER_STYLE = 'forever_general/footer/style';
    const XML_PATH_STICKY_HEADER_TYPE = 'forever_general/header/stickyheader';
    const XML_PATH_STICKY_HEADER_TYPE_ENABLE = 'forever_general/header/sticky';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $nextPrevious;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $categoryModel;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param Psr\Log\LoggerInterface $logger
     * @param Magento\Catalog\Model\Category $categoryModel
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\Category $categoryModel,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->logger = $logger;
        $this->categoryModel = $categoryModel;
        $this->customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * get header stype from admin configuration
     *
     * @return string $headerStyle
     */
    public function getHeaderStyle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HEADER_STYLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get footer stype from admin configuration
     *
     * @return string $footerStyle
     */
    public function getfooterStyle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FOOTER_STYLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getconfigValue($path)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getCustomerLogin()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * get sticky header type from system configuration
     *
     * @return string
     */
    public function getStickyHeaderType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STICKY_HEADER_TYPE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get sticky header type enable from system configuration
     *
     * @return integer
     */
    public function isStickyEnable()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_STICKY_HEADER_TYPE_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
