<?php
namespace Forever\DemoInstaller\Model\Importer;

use Forever\DemoInstaller\Api\ImporterInterface;
use Forever\DemoInstaller\Model\ImportContext;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Framework\Filesystem\Driver\File as FileDriver;

class CmsPageImporter implements ImporterInterface
{
    /** Keys we accept from the XML cms_item node. */
    private const FIELDS = [
        'title', 'page_layout', 'meta_keywords', 'meta_description',
        'content_heading', 'content', 'layout_update_xml', 'custom_theme', 'is_active'
    ];

    public function __construct(
        private CollectionFactory $collectionFactory,
        private PageFactory $pageFactory,
        private PageRepositoryInterface $pageRepository,
        private FileDriver $file
    ) {}

    public function import(array $step, ImportContext $context): void
    {
        $path = $context->file($step['source'] ?? 'cms_pages.xml');
        if (!$this->file->isReadable($path)) {
            $context->addMessage('  - cms_page: file missing, skipped (' . basename($path) . ')');
            return;
        }
        $parser = new \Magento\Framework\Xml\Parser();
        $data = $parser->load($path)->xmlToArray();
        $items = $this->normalize($data['root']['pages']['cms_item'] ?? []);

        $created = 0;
        $updated = 0;
        foreach ($items as $item) {
            if (empty($item['identifier'])) {
                continue;
            }
            $payload = ['identifier' => $item['identifier'], 'is_active' => 1, 'stores' => [0]];
            foreach (self::FIELDS as $f) {
                if (array_key_exists($f, $item)) {
                    $payload[$f] = $item[$f];
                }
            }

            $existing = $this->collectionFactory->create()
                ->addFieldToFilter('identifier', $item['identifier'])
                ->getFirstItem();

            if ($existing->getId()) {
                if (!$context->isOverwrite()) {
                    continue;
                }
                $existing->addData($payload);
                $this->pageRepository->save($existing);
                $updated++;
            } else {
                $page = $this->pageFactory->create()->setData($payload);
                $this->pageRepository->save($page);
                $created++;
            }
        }
        $context->addMessage(sprintf('  - cms_page: %d created, %d updated', $created, $updated));
    }

    private function normalize(array $items): array
    {
        return isset($items['identifier']) ? [$items] : $items;
    }
}
