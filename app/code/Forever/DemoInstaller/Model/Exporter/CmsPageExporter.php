<?php
namespace Forever\DemoInstaller\Model\Exporter;

use Forever\DemoInstaller\Api\ExporterInterface;
use Forever\DemoInstaller\Model\ExportContext;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Framework\Filesystem\Driver\File as FileDriver;

/**
 * Dumps CMS pages into the <root><pages><cms_item> XML format the importer reads.
 * Step: { "type":"cms_page", "source":"cms_pages.xml", "identifiers":["home","about-us"] }
 * "identifiers" is optional - omit to export every page.
 */
class CmsPageExporter implements ExporterInterface
{
    /** Simple (non-CDATA) fields emitted as plain text nodes. */
    private const TEXT_FIELDS = [
        'title', 'page_layout', 'meta_keywords', 'meta_description', 'content_heading'
    ];
    /** Fields emitted inside CDATA. */
    private const CDATA_FIELDS = ['content', 'layout_update_xml'];

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
        $pages = $root->appendChild($dom->createElement('pages'));

        $seen = [];
        $count = 0;
        foreach ($collection as $page) {
            $identifier = (string)$page->getIdentifier();
            if ($identifier === '' || isset($seen[$identifier])) {
                continue;
            }
            $seen[$identifier] = true;

            $item = $pages->appendChild($dom->createElement('cms_item'));
            $item->appendChild($dom->createElement('identifier'))->appendChild($dom->createTextNode($identifier));

            foreach (self::TEXT_FIELDS as $f) {
                $value = (string)$page->getData($f);
                if ($value !== '') {
                    $item->appendChild($dom->createElement($f))->appendChild($dom->createTextNode($value));
                }
            }
            foreach (self::CDATA_FIELDS as $f) {
                $value = (string)$page->getData($f);
                if ($value !== '') {
                    $node = $item->appendChild($dom->createElement($f));
                    $node->appendChild($dom->createCDATASection($value));
                }
            }
            $count++;
        }

        $dest = $context->file($step['source'] ?? 'cms_pages.xml');
        $this->writeFile($dest, $dom->saveXML());
        $context->addMessage(sprintf('  - cms_page: %d exported', $count));
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
