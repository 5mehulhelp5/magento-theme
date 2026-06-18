<?php
namespace Forever\DemoInstaller\Model;

use Forever\DemoInstaller\Api\ExporterInterface;
use Magento\Framework\Exception\LocalizedException;

class ExporterPool
{
    /** @var ExporterInterface[] */
    private array $exporters;

    /** @param ExporterInterface[] $exporters */
    public function __construct(array $exporters = [])
    {
        $this->exporters = $exporters;
    }

    public function get(string $type): ExporterInterface
    {
        if (!isset($this->exporters[$type])) {
            throw new LocalizedException(__('No demo exporter registered for type "%1".', $type));
        }
        return $this->exporters[$type];
    }
}
