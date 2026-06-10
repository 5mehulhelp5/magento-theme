<?php

namespace Forever\NewsletterPopup\Setup\Patch\Data;
 
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Store\Model\Store;

class NewsletterStaticBlock implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;
 
    /**
     * @var Magento\Cms\Model\BlockFactory
     */
    private $blockFactory;
 
    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Cms\Model\BlockFactory $blockFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->blockFactory = $blockFactory;
    }
 
    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $newsletterStaticBlock = [
            'title' => 'Forever Newslettee Popup',
            'identifier' => 'forever_newsletter_popup',
            'content' => '
                <p>You will get email notifications receive huge price discounts
                including free shipping offers and the latest new products.
                </p>',
            'is_active' => 1,
            'stores' => Store::DEFAULT_STORE_ID
        ];
 
        $this->moduleDataSetup->startSetup();
        $block = $this->blockFactory->create();
        $block->setData($newsletterStaticBlock)->save();
 
        $this->moduleDataSetup->endSetup();
    }
 
    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
 
    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '2.0.0';
    }
 
    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
