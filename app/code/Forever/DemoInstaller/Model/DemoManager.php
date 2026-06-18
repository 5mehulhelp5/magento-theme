<?php
namespace Forever\DemoInstaller\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Cache\TypeListInterface;
use Psr\Log\LoggerInterface;

class DemoManager
{
    public function __construct(
        private DemoRepository $demoRepository,
        private ImporterPool $importerPool,
        private TypeListInterface $cacheTypeList,
        private LoggerInterface $logger
    ) {}

    /**
     * Import a demo package.
     *
     * @param string   $code        Demo code
     * @param int      $storeId     Target store view id (0 = default)
     * @param string[] $onlyTypes   Restrict to these step types (empty = all)
     * @param bool     $overwrite   Update existing records
     * @param bool     $importMedia Copy media files
     * @return ImportContext  Carries the collected messages
     */
    public function import(
        string $code,
        int $storeId = 0,
        array $onlyTypes = [],
        bool $overwrite = true,
        bool $importMedia = true
    ): ImportContext {
        $demo = $this->demoRepository->get($code);

        $context = (new ImportContext())
            ->setPackagePath($demo->getPath())
            ->setStoreId($storeId)
            ->setOverwrite($overwrite)
            ->setImportMedia($importMedia);

        $context->addMessage(sprintf('Importing demo "%s" (v%s)...', $demo->getLabel(), $demo->getVersion()));

        foreach ($demo->getSteps() as $step) {
            $type = $step['type'] ?? null;
            if (!$type) {
                continue;
            }
            if ($onlyTypes && !in_array($type, $onlyTypes, true)) {
                continue;
            }
            try {
                $this->importerPool->get($type)->import($step, $context);
            } catch (\Throwable $e) {
                $this->logger->error('[DemoInstaller] step "' . $type . '" failed: ' . $e->getMessage(), ['exception' => $e]);
                $context->addMessage(sprintf('  [x] %s: %s', $type, $e->getMessage()));
                if (!empty($step['required'])) {
                    throw new LocalizedException(
                        __('Required step "%1" failed: %2', $type, $e->getMessage())
                    );
                }
            }
        }

        $this->cacheTypeList->cleanType('config');
        $this->cacheTypeList->cleanType('layout');
        $this->cacheTypeList->cleanType('block_html');
        $this->cacheTypeList->cleanType('full_page');

        $context->addMessage('Done. Flush the cache and reindex if needed.');
        return $context;
    }
}
