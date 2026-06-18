<?php
namespace Forever\DemoInstaller\Model\Exporter;

use Forever\DemoInstaller\Api\ExporterInterface;
use Forever\DemoInstaller\Model\ExportContext;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Framework\Filesystem\Driver\File as FileDriver;

/**
 * Dumps CMS blocks into the <root><blocks><cms_item> XML format the importer reads.
 * Step: { "type":"cms_block", "source":"cms_blocks.xml", "identifiers":["..."] }
 * "identifiers" is optional - omit to export every block.
 */
class CmsBlockExporter implements ExporterInterface
{
    public function __construct(
        private CollectionFactory $collectionFactory,
        private FileDriver $file
    ) {}

    public function export(array $step, ExportContext $context): void
    {
        $collection = $this->collectionFactory->create();
        if (!empty($step['identifiers'])) {
            $collection->addFieldToFilter('identifier', ['in' => $step['identifiers']]);
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $root = $dom->appendChild($dom->createElement('root'));
        $blocks = $root->appendChild($dom->createElement('blocks'));

        $seen = [];
        $count = 0;
        foreach ($collection as $block) {
            $identifier = (string)$block->getIdentifier();
            if ($identifier === '' || isset($seen[$identifier])) {
                continue;
            }
            $seen[$identifier] = true;

            $item = $blocks->appendChild($dom->createElement('cms_item'));
            $item->appendChild($dom->createElement('title'))->appendChild($dom->createTextNode((string)$block->getTitle()));
            $item->appendChild($dom->createElement('identifier'))->appendChild($dom->createTextNode($identifier));
            $content = $item->appendChild($dom->createElement('content'));
            $content->appendChild($dom->createCDATASection((string)$block->getContent()));
            $count++;
        }

        $dest = $context->file($step['source'] ?? 'cms_blocks.xml');
        $this->writeFile($dest, $dom->saveXML());
        $context->addMessage(sprintf('  - cms_block: %d exported', $count));
    }

    private function writeFile(string $path, string $contents): void
    {
        $dir = dirname($path);
        if (!$this->file->isDirectory($dir)) {
            $this->file->createDirectory($dir, 0775);
        }
        $this->file->filePutContents($path, $contents);
    }
}
