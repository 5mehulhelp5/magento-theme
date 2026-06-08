<?php

// @codingStandardsIgnoreFile
namespace Forever\Core\Model\Cssconfig;

class Generator
{
    /**
     * @var Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var Magento\Framework\View\LayoutInterface
     */
    protected $layoutManager;

    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Forever\Core\Helper\Cssconfig
     */
    protected $cssconfigData;

    /**
     * @param Magento\Framework\Registry $coreRegistry
     * @param Magento\Framework\View\LayoutInterface $layoutManager
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Magento\Framework\Message\ManagerInterface $messageManager
     * @param Forever\Core\Helper\Cssconfig $cssconfigData
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\LayoutInterface $layoutManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Forever\Core\Helper\Cssconfig $cssconfigData
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->layoutManager = $layoutManager;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->cssconfigData = $cssconfigData;
    }
    
    public function generateCss($type, $websiteId, $storeId)
    {
        if (!$websiteId && !$storeId) {
            $websites = $this->storeManager->getWebsites(false, false);
            foreach ($websites as $id => $value) {
                $this->generateWebsiteCss($type, $id);
            }
        } else {
            if ($storeId) {
                $this->generateStoreCss($type, $storeId);
            } else {
                $this->generateWebsiteCss($type, $websiteId);
            }
        }
    }
    
    protected function generateWebsiteCss($type, $websiteId)
    {
        $website = $this->storeManager->getWebsite($websiteId);
        foreach ($website->getStoreIds() as $storeId) {
            $this->generateStoreCss($type, $storeId);
        }
    }

    protected function generateStoreCss($type, $storeId)
    {
        $store = $this->storeManager->getStore($storeId);
        if (!$store->isActive()) {
            return;
        }
        $storeCode = $store->getCode();
        $str1 = '_' . $storeCode;
        $str2 = $type . $str1 . '.css';
        $str3 = $this->cssconfigData->getCssConfigDir() . $str2;
        $str4 = 'forever/css/' . $type . '.phtml';
        $this->coreRegistry->register('cssgen_store', $storeCode);

        try {
            $block = $this->layoutManager->createBlock(\Forever\Core\Block\Template::class)
                ->setData('area', 'frontend')->setTemplate($str4)->toHtml();
            if (!file_exists($this->cssconfigData->getCssConfigDir())) {
                @mkdir($this->cssconfigData->getCssConfigDir(), 0777);
            }
            $file = @fopen($str3, "w+");
            @flock($file, LOCK_EX);
            @fwrite($file, $block);
            @flock($file, LOCK_UN);
            @fclose($file);
            if (empty($block)) {
                $this->messageManager->addErrorMessage(
                    __("Template file is empty or doesn't exist: " . $str4)
                );
            }
        } catch (\Exception $e) {
            $configDir = $this->cssconfigData->getCssConfigDir();
            $this->messageManager->addErrorMessage(
                __('Failed generating CSS file: ' . $str2 . ' in ' . $configDir) . '<br/>Message: ' . $e->getMessage()
            );
        }
        $this->coreRegistry->unregister('cssgen_store');
    }
}
