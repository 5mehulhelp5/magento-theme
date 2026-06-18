<?php
namespace Forever\DemoInstaller\Model\Exporter;

use Forever\DemoInstaller\Api\ExporterInterface;
use Forever\DemoInstaller\Model\ExportContext;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Serialize\Serializer\Json;

class GenericTableExporter implements ExporterInterface
{
    public function __construct(
        private ResourceConnection $resource,
        private FileDriver $file,
        private Json $json
    ) {}

    public function export(array $step, ExportContext $context): void
    {
        $table = $step['table'] ?? null;
        if (!$table) {
            return;
        }
        $connection = $this->resource->getConnection();
        $realTable  = $this->resource->getTableName($table);
        if (!$connection->isTableExists($realTable)) {
            $context->addMessage('  - table ' . $table . ': not present, skipped');
            return;
        }
        $select = $connection->select()->from($realTable);
        if (!empty($step['where'])) {
            $select->where($step['where']);
        }
        $rows = $connection->fetchAll($select);
        $dest = $context->file($step['source'] ?? ($table . '.json'));
        $this->writeJson($dest, $rows);
        $context->addMessage(sprintf('  - table %s: %d row(s) exported', $table, count($rows)));
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
