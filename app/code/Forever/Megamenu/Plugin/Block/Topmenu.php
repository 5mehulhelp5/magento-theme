<?php

namespace Forever\Megamenu\Plugin\Block;

class Topmenu
{
    /**
     * @var \Forever\Megamenu\Helper\Data
     */
    protected $helper;

    /**
     * @param \Forever\Megamenu\Helper\Data $helper
     */
    public function __construct(
        \Forever\Megamenu\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Filter top menu categories to exclude Luma default categories
     *
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return void
     */
    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    ) {
        $menuConfig = $this->helper->getConfigModule();
        $isMegaMenuEnabled = isset($menuConfig['module']['enabled']) && $menuConfig['module']['enabled'];
        $isHomeButtonEnabled = isset($menuConfig['homebutton']['homebuttonenabled'])
            && $menuConfig['homebutton']['homebuttonenabled'];

        // If Mega Menu is enabled, drawMainMenu() already handles the home item.
        if ($isMegaMenuEnabled) {
            return;
        }

        $menu = $subject->getMenu();
        if (!$menu) {
            return;
        }

        // Luma category IDs to exclude
        $excludeIds = [
            'category-node-69',   // Gear
            'category-node-75',   // Training
            'category-node-77',   // Men
            'category-node-86',   // Women
            'category-node-103',  // Sale
            'category-node-104'   // What's New
        ];

        foreach ($menu->getChildren() as $child) {
            if (in_array($child->getId(), $excludeIds, true)
                || (!$isHomeButtonEnabled && $this->isHomeNode($child))
            ) {
                $menu->removeChild($child);
            }
        }
    }

    /**
     * Add Forever menu settings to native top menu cache key.
     *
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param array $result
     * @return array
     */
    public function afterGetCacheKeyInfo(
        \Magento\Theme\Block\Html\Topmenu $subject,
        array $result
    ) {
        $menuConfig = $this->helper->getConfigModule();
        $result[] = 'forever_megamenu_enabled_'
            . (int) (!empty($menuConfig['module']['enabled']));
        $result[] = 'forever_megamenu_home_'
            . (int) (!empty($menuConfig['homebutton']['homebuttonenabled']));

        return $result;
    }

    /**
     * Check native menu node is the Home menu item.
     *
     * @param \Magento\Framework\Data\Tree\Node $node
     * @return bool
     */
    private function isHomeNode(\Magento\Framework\Data\Tree\Node $node)
    {
        return strcasecmp(trim((string) $node->getName()), 'home') === 0;
    }
}
