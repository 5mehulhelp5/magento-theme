<?php
namespace Forever\DemoInstaller\Model\Importer;

use Forever\DemoInstaller\Api\ImporterInterface;
use Forever\DemoInstaller\Model\ImportContext;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Framework\Filesystem\Driver\File as FileDriver;

class CmsBlockImporter implements ImporterInterface
{
    public function __construct(
        private CollectionFactory $collectionFactory,
        private BlockFactory $blockFactory,
        private BlockRepositoryInterface $blockRepository,
        private FileDriver $file
    ) {}

    public function import(array $step, ImportContext $context): void
    {
        $path = $context->file($step['source'] ?? 'cms_blocks.xml');
        if (!$this->file->isReadable($path)) {
            $context->addMessage('  - cms_block: file missing, skipped (' . basename($path) . ')');
            return;
        }
        $parser = new \Magento\Framework\Xml\Parser();
        $data = $parser->load($path)->xmlToArray();
        $items = $this->normalize($data['root']['blocks']['cms_item'] ?? []);

        $created = 0;
        $updated = 0;
        foreach ($items as $item) {
            if (empty($item['identifier'])) {
                continue;
            }
            $existing = $this->collectionFactory->create()
                ->addFieldToFilter('identifier', $item['identifier'])
                ->getFirstItem();

            if ($existing->getId()) {
                if (!$context->isOverwrite()) {
                    continue;
                }
                $existing->addData([
                    'title'     => $item['title'] ?? $existing->getTitle(),
                    'content'   => $item['content'] ?? '',
                    'is_active' => 1,
                ]);
                $this->blockRepository->save($existing);
                $updated++;
            } else {
                $block = $this->blockFactory->create();
                $block->setData([
                    'title'      => $item['title'] ?? $item['identifier'],
                    'identifier' => $item['identifier'],
                    'content'    => $item['content'] ?? '',
                    'is_active'  => 1,
                    'stores'     => [0],
                ]);
                $this->blockRepository->save($block);
                $created++;
            }
        }
        $context->addMessage(sprintf('  - cms_block: %d created, %d updated', $created, $updated));
    }

    private function normalize(array $items): array
    {
        // xmlToArray returns a single assoc array when there is only one node
        return isset($items['identifier']) ? [$items] : $items;
    }
}
