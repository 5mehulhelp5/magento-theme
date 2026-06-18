<?php
namespace Forever\DemoInstaller\Model\Importer;

use Forever\DemoInstaller\Api\ImporterInterface;
use Forever\DemoInstaller\Model\ImportContext;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;

/**
 * Step: { "type":"config", "source":"config.json" }
 * config.json: [ { "path":"design/theme/theme_id", "value":"...", "scope":"stores", "scope_id":1 }, ... ]
 */
class ConfigImporter implements ImporterInterface
{
    public function __construct(
        private WriterInterface $configWriter,
        private FileDriver $file,
        private Json $json
    ) {}

    public function import(array $step, ImportContext $context): void
    {
        $path = $context->file($step['source'] ?? 'config.json');
        if (!$this->file->isReadable($path)) {
            $context->addMessage('  - config: file missing, skipped');
            return;
        }
        $entries = $this->json->unserialize($this->file->fileGetContents($path));
        $count = 0;
        foreach ($entries as $entry) {
            if (empty($entry['path'])) {
                continue;
            }
            $scope   = $entry['scope'] ?? ScopeInterface::SCOPE_STORES;
            $scopeId = isset($entry['scope_id']) ? (int)$entry['scope_id'] : $context->getStoreId();
            if ($scope === 'default') {
                $scopeId = 0;
            }
            $this->configWriter->save($entry['path'], (string)($entry['value'] ?? ''), $scope, $scopeId);
            $count++;
        }
        $context->addMessage(sprintf('  - config: %d value(s) set', $count));
    }
}
