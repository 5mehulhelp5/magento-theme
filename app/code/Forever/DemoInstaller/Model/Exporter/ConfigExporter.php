<?php
namespace Forever\DemoInstaller\Model\Exporter;

use Forever\DemoInstaller\Api\ExporterInterface;
use Forever\DemoInstaller\Model\ExportContext;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem\Driver\File as FileDriver;

/**
 * Step: { "type":"config", "source":"config.json", "paths":["forever_general/","design/"] }
 */
class ConfigExporter implements ExporterInterface
{
    public function __construct(
        private ResourceConnection $resource,
        private FileDriver $file
    ) {}

    public function export(array $step, ExportContext $context): void
    {
        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('core_config_data');
        $select = $connection->select()->from($table, ['path', 'value', 'scope', 'scope_id']);

        $paths = $step['paths'] ?? [];
        if ($paths) {
            $cond = [];
            foreach ($paths as $p) {
                $cond[] = $connection->quoteInto('path LIKE ?', $p . '%');
            }
            $select->where(implode(' OR ', $cond));
        }
        $rows = $connection->fetchAll($select);
        $dest = $context->file($step['source'] ?? 'config.json');
        $this->writeJson($dest, $rows);
        $context->addMessage(sprintf('  - config: %d value(s) exported', count($rows)));
    }

    private function writeJson(string $path, $data): void
    {
        $dir = dirname($path);
        if (!$this->file->isDirectory($dir)) {
            $this->file->createDirectory($dir, 0775);
        }
        $this->file->filePutContents(
            $path,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }
}
