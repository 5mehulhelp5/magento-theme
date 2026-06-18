<?php
namespace Forever\DemoInstaller\Model\Importer;

use Forever\DemoInstaller\Api\ImporterInterface;
use Forever\DemoInstaller\Model\ImportContext;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Step: { "type":"theme", "theme":"frontend/Forever/furniture" }
 */
class ThemeImporter implements ImporterInterface
{
    public function __construct(
        private ThemeProviderInterface $themeProvider,
        private WriterInterface $configWriter
    ) {}

    public function import(array $step, ImportContext $context): void
    {
        $full = $step['theme'] ?? null;
        if (!$full) {
            $context->addMessage('  - theme: no "theme" in step, skipped');
            return;
        }
        if (strpos($full, '/') !== false && strpos($full, 'frontend/') !== 0) {
            $full = 'frontend/' . $full; // accept "Forever/furniture" too
        }
        $theme = $this->themeProvider->getThemeByFullPath($full);
        if (!$theme || !$theme->getId()) {
            $context->addMessage('  - theme: "' . $full . '" not found/registered, skipped');
            return;
        }
        $this->configWriter->save(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            (string)$theme->getId(),
            $context->getStoreId() ? ScopeInterface::SCOPE_STORES : 'default',
            $context->getStoreId()
        );
        $context->addMessage('  - theme: assigned "' . $full . '" (id ' . $theme->getId() . ')');
    }
}
