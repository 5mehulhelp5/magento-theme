<?php
namespace Forever\DemoInstaller\Model;

use Magento\Framework\Filesystem\Driver\File as FileDriver;

class ExportManager
{
    public function __construct(
        private DemoRepository $demoRepository,
        private ExporterPool $exporterPool,
        private FileDriver $file
    ) {}

    public function export(string $code, int $storeId = 0, array $onlyTypes = []): ExportContext
    {
        $packagePath = $this->demoRepository->getBaseDir() . '/' . $code;
        if (!$this->file->isDirectory($packagePath)) {
            $this->file->createDirectory($packagePath, 0775);
        }
        // Requires an existing manifest.json describing the steps to export.
        $demo = $this->demoRepository->get($code);

        $context = (new ExportContext())->setPackagePath($packagePath)->setStoreId($storeId);
        foreach ($demo->getSteps() as $step) {
            $type = $step['type'] ?? null;
            if (!$type || $type === 'media' || $type === 'theme') {
                continue;
            }
            if ($onlyTypes && !in_array($type, $onlyTypes, true)) {
                continue;
            }
            try {
                $this->exporterPool->get($type)->export($step, $context);
            } catch (\Throwable $e) {
                $context->addMessage(sprintf('  [x] %s: %s', $type, $e->getMessage()));
            }
        }
        return $context;
    }
}
