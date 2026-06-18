<?php
namespace Forever\DemoInstaller\Model\Importer;

use Forever\DemoInstaller\Api\ImporterInterface;
use Forever\DemoInstaller\Model\ImportContext;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory;
use Magento\Widget\Model\Widget\Instance as WidgetInstance;

/**
 * Step: { "type":"widget", "source":"widgets.json" }
 * widgets.json: [ { "title":"...", "instance_type":"...", "theme_full_path":"frontend/Forever/furniture",
 *                   "store_ids":[0], "sort_order":0,
 *                   "widget_parameters":{...}, "page_groups":[ {...} ] }, ... ]
 */
class WidgetImporter implements ImporterInterface
{
    public function __construct(
        private CollectionFactory $collectionFactory,
        private \Magento\Widget\Model\Widget\InstanceFactory $instanceFactory,
        private \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider,
        private FileDriver $file,
        private Json $json
    ) {}

    public function import(array $step, ImportContext $context): void
    {
        $path = $context->file($step['source'] ?? 'widgets.json');
        if (!$this->file->isReadable($path)) {
            $context->addMessage('  - widget: file missing, skipped');
            return;
        }
        $widgets = $this->json->unserialize($this->file->fileGetContents($path));
        $count = 0;
        foreach ($widgets as $w) {
            if (empty($w['instance_type']) || empty($w['title'])) {
                continue;
            }

            $themeId = $w['theme_id'] ?? null;
            if (!$themeId && !empty($w['theme_full_path'])) {
                $theme = $this->themeProvider->getThemeByFullPath($w['theme_full_path']);
                $themeId = $theme ? $theme->getId() : null;
            }

            // De-dup on title + type.
            $existing = $this->collectionFactory->create()
                ->addFieldToFilter('title', $w['title'])
                ->addFieldToFilter('instance_type', $w['instance_type'])
                ->getFirstItem();

            if ($existing->getId() && !$context->isOverwrite()) {
                continue;
            }

            /** @var WidgetInstance $instance */
            $instance = $existing->getId() ? $existing : $this->instanceFactory->create();
            $instance->setType($w['instance_type']);
            if ($themeId) {
                $instance->setThemeId($themeId);
            }
            $instance->setTitle($w['title']);
            $instance->setStoreIds(implode(',', $w['store_ids'] ?? [0]));
            $instance->setWidgetParameters($w['widget_parameters'] ?? []);
            $instance->setSortOrder((int)($w['sort_order'] ?? 0));
            if (!empty($w['page_groups'])) {
                $instance->setData('page_groups', $w['page_groups']);
            }
            $instance->save();
            $count++;
        }
        $context->addMessage(sprintf('  - widget: %d instance(s) imported', $count));
    }
}
