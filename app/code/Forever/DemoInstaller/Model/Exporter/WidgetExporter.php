<?php
namespace Forever\DemoInstaller\Model\Exporter;

use Forever\DemoInstaller\Api\ExporterInterface;
use Forever\DemoInstaller\Model\ExportContext;
use Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\Filesystem\Driver\File as FileDriver;

class WidgetExporter implements ExporterInterface
{
    public function __construct(
        private CollectionFactory $collectionFactory,
        private ThemeProviderInterface $themeProvider,
        private FileDriver $file
    ) {}

    public function export(array $step, ExportContext $context): void
    {
        $out = [];
        foreach ($this->collectionFactory->create() as $w) {
            $w->load($w->getId()); // hydrate page_groups + parameters

            // Translate numeric theme id -> portable full path (e.g. frontend/Forever/furniture)
            $themeFullPath = null;
            if ($w->getThemeId()) {
                $theme = $this->themeProvider->getThemeById((int)$w->getThemeId());
                $themeFullPath = $theme ? $theme->getFullPath() : null;
            }

            // getStoreIds() may return an array or a comma-separated string depending on load path.
            $storeIds = $w->getStoreIds();
            if (!is_array($storeIds)) {
                $storeIds = array_filter(explode(',', (string)$storeIds), 'strlen');
            }

            $out[] = [
                'title'             => $w->getTitle(),
                'instance_type'     => $w->getType(),
                'theme_full_path'   => $themeFullPath,
                'store_ids'         => array_values($storeIds),
                'sort_order'        => (int)$w->getSortOrder(),
                'widget_parameters' => $w->getWidgetParameters(),
                'page_groups'       => $w->getPageGroups(),
            ];
        }
        $dest = $context->file($step['source'] ?? 'widgets.json');
        $dir = dirname($dest);
        if (!$this->file->isDirectory($dir)) {
            $this->file->createDirectory($dir, 0775);
        }
        $this->file->filePutContents(
            $dest,
            json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
        $context->addMessage(sprintf('  - widget: %d exported', count($out)));
    }
}
