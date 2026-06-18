<?php
namespace Forever\DemoInstaller\Model\Importer;

use Forever\DemoInstaller\Api\ImporterInterface;
use Forever\DemoInstaller\Model\ImportContext;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File as FileDriver;

/**
 * Step: { "type":"media", "source":"media" }   copies package/media/* into pub/media/*
 */
class MediaImporter implements ImporterInterface
{
    public function __construct(
        private Filesystem $filesystem,
        private FileDriver $file
    ) {}

    public function import(array $step, ImportContext $context): void
    {
        if (!$context->isImportMedia()) {
            $context->addMessage('  - media: skipped (disabled)');
            return;
        }
        $sourceRoot = $context->file($step['source'] ?? 'media');
        if (!$this->file->isDirectory($sourceRoot)) {
            $context->addMessage('  - media: no media folder, skipped');
            return;
        }
        $mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $count = $this->copyTree($sourceRoot, '', $mediaDir, (bool)($step['overwrite'] ?? false));
        $context->addMessage(sprintf('  - media: %d file(s) copied', $count));
    }

    private function copyTree(string $absRoot, string $rel, $mediaDir, bool $overwrite): int
    {
        $count = 0;
        $current = $rel === '' ? $absRoot : $absRoot . '/' . $rel;
        foreach ($this->file->readDirectory($current) as $path) {
            $name    = basename($path);
            $relPath = $rel === '' ? $name : $rel . '/' . $name;
            if ($this->file->isDirectory($path)) {
                $count += $this->copyTree($absRoot, $relPath, $mediaDir, $overwrite);
            } else {
                if ($overwrite || !$mediaDir->isExist($relPath)) {
                    $mediaDir->writeFile($relPath, $this->file->fileGetContents($path));
                    $count++;
                }
            }
        }
        return $count;
    }
}
