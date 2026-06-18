<?php
namespace Forever\DemoInstaller\Model;

use Forever\DemoInstaller\Api\ImporterInterface;
use Magento\Framework\Exception\LocalizedException;

class ImporterPool
{
    /** @var ImporterInterface[] */
    private array $importers;

    /** @param ImporterInterface[] $importers */
    public function __construct(array $importers = [])
    {
        $this->importers = $importers;
    }

    public function get(string $type): ImporterInterface
    {
        if (!isset($this->importers[$type])) {
            throw new LocalizedException(__('No demo importer registered for type "%1".', $type));
        }
        $importer = $this->importers[$type];
        if (!$importer instanceof ImporterInterface) {
            throw new LocalizedException(__('Importer "%1" must implement ImporterInterface.', $type));
        }
        return $importer;
    }
}
