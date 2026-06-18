<?php
namespace Forever\DemoInstaller\Model\Importer;

use Forever\DemoInstaller\Api\ImporterInterface;
use Forever\DemoInstaller\Model\ImportContext;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Column-agnostic upsert into any custom table.
 * Step: { "type":"table", "source":"bannerslider.json", "table":"bannerslider",
 *         "unique":["imagetext"], "truncate":false }
 */
class GenericTableImporter implements ImporterInterface
{
    public function __construct(
        private ResourceConnection $resource,
        private FileDriver $file,
        private Json $json
    ) {}

    public function import(array $step, ImportContext $context): void
    {
        $table = $step['table'] ?? null;
        if (!$table) {
            $context->addMessage('  - table: no "table" in step, skipped');
            return;
        }
        $path = $context->file($step['source'] ?? ($table . '.json'));
        if (!$this->file->isReadable($path)) {
            $context->addMessage('  - table ' . $table . ': file missing, skipped');
            return;
        }

        $rows = $this->json->unserialize($this->file->fileGetContents($path));
        if (!is_array($rows) || !$rows) {
            $context->addMessage('  - table ' . $table . ': no rows');
            return;
        }

        $connection = $this->resource->getConnection();
        $realTable  = $this->resource->getTableName($table);

        if (!$connection->isTableExists($realTable)) {
            $context->addMessage('  - table ' . $table . ': does not exist, skipped');
            return;
        }

        // Restrict to columns that actually exist (schema-safe).
        $describe  = $connection->describeTable($realTable);
        $validCols = array_keys($describe);
        $unique    = array_values(array_intersect($step['unique'] ?? [], $validCols));

        $connection->beginTransaction();
        try {
            if (!empty($step['truncate'])) {
                $connection->delete($realTable);
            }

            $count = 0;
            foreach ($rows as $row) {
                $data = array_intersect_key($row, array_flip($validCols));
                if (!$data) {
                    continue;
                }

                if ($context->isOverwrite()) {
                    // ON DUPLICATE KEY UPDATE keys off the table's own PRIMARY/UNIQUE
                    // indexes automatically, so we do not need to know the PK column name
                    // (e.g. forever_blog uses blog_id, others use id).
                    $connection->insertOnDuplicate($realTable, $data, array_keys($data));
                } else {
                    // No-overwrite: skip if a matching row already exists (by the given unique key).
                    if ($unique) {
                        $select = $connection->select()
                            ->from($realTable, ['cnt' => new \Zend_Db_Expr('COUNT(*)')]);
                        foreach ($unique as $u) {
                            $select->where($connection->quoteIdentifier($u) . ' = ?', $row[$u] ?? null);
                        }
                        if ((int)$connection->fetchOne($select) > 0) {
                            continue;
                        }
                    }
                    $connection->insert($realTable, $data);
                }
                $count++;
            }
            $connection->commit();
            $context->addMessage(sprintf('  - table %s: %d row(s) imported', $table, $count));
        } catch (\Throwable $e) {
            $connection->rollBack();
            throw $e;
        }
    }
}
