<?php
namespace Forever\DemoInstaller\Model\Importer;

use Forever\DemoInstaller\Api\ImporterInterface;
use Forever\DemoInstaller\Model\ImportContext;
use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Deploy\Model\DeploymentConfig\Hash;

/**
 * Writes a demo's configuration as STATIC, file-based values into app/etc/config.php
 * (or app/etc/env.php), exactly like the "system" => "default" block you see in env.php.
 * Values written here become locked/greyed-out in the admin (shared configuration).
 *
 * This is the file-based alternative to the DB-based "config" importer.
 *
 * Manifest step:
 *   { "type":"deployment_config", "source":"deployment_config.php", "target":"config", "override":false }
 *
 * - source : a .php file that returns an array, or a .json file. Accepted shapes:
 *      (a) a full/partial env.php style array containing a "system" key  -> only "system" is applied
 *      (b) a flat list [ { "path":"web/unsecure/base_url", "value":"...", "scope":"default" }, ... ]
 *      (c) a plain nested tree (web/forever_general/...) -> wrapped under system/default
 * - target : "config" -> app/etc/config.php (default, version-controllable),
 *            "env"    -> app/etc/env.php
 * - override : false (default) merges with what's already in the file; true replaces the pool.
 *              Keep it false so the existing "modules"/"scopes" keys are preserved.
 *
 * SAFETY: only the "system" portion is ever written. db/crypt/session/cache_types and other
 * environment secrets are ignored even if present in the source file.
 */
class DeploymentConfigImporter implements ImporterInterface
{
    public function __construct(
        private Writer $deploymentWriter,
        private FileDriver $file,
        private Json $json,
        private Hash $configHash
    ) {}

    public function import(array $step, ImportContext $context): void
    {
        $source = $step['source'] ?? 'deployment_config.php';
        $path = $context->file($source);
        if (!$this->file->isReadable($path)) {
            $context->addMessage('  - deployment_config: file missing, skipped (' . basename($path) . ')');
            return;
        }

        $data = $this->load($path, $source);
        if (!is_array($data) || !$data) {
            $context->addMessage('  - deployment_config: empty source, skipped');
            return;
        }

        $configData = $this->extractConfig($data);
        if (empty($configData['system'])) {
            $context->addMessage('  - deployment_config: no "system" values found, skipped');
            return;
        }

        $pool = (($step['target'] ?? 'config') === 'env')
            ? ConfigFilePool::APP_ENV
            : ConfigFilePool::APP_CONFIG;

        $override = (bool)($step['override'] ?? false);

        $this->deploymentWriter->saveConfig([$pool => $configData], $override);

        // Re-sync the deployment-config hash so Magento's ConfigChangeDetector does not
        // block every request demanding "app:config:import" after we changed config.php.
        $this->configHash->regenerate();

        $count = $this->countLeaves($configData['system']);
        $target = $pool === ConfigFilePool::APP_ENV ? 'app/etc/env.php' : 'app/etc/config.php';
        $context->addMessage(sprintf('  - deployment_config: %d value(s) written to %s (static/locked, hash synced)', $count, $target));
    }

    private function load(string $path, string $source): array
    {
        if (substr($source, -4) === '.php') {
            // The file returns an array.
            $data = include $path;
            return is_array($data) ? $data : [];
        }
        $decoded = $this->json->unserialize($this->file->fileGetContents($path));
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Reduce any accepted source shape to ['system' => ['default' => [...], 'stores' => [...] ...]].
     */
    private function extractConfig(array $data): array
    {
        // (a) Already has a system block (e.g. a copied env.php) -> use only that.
        if (isset($data['system']) && is_array($data['system'])) {
            return ['system' => $data['system']];
        }

        // (b) Flat list of path/value entries.
        if ($this->isFlatList($data)) {
            return $this->buildFromFlatList($data);
        }

        // (c) Plain nested tree -> assume default scope.
        return ['system' => ['default' => $data]];
    }

    private function isFlatList(array $data): bool
    {
        if (!array_key_exists(0, $data)) {
            return false;
        }
        return is_array($data[0]) && array_key_exists('path', $data[0]);
    }

    private function buildFromFlatList(array $entries): array
    {
        $tree = [];
        foreach ($entries as $entry) {
            if (empty($entry['path'])) {
                continue;
            }
            $scope   = $entry['scope'] ?? 'default';
            // Deployment config uses scope CODE, not id. We support "default" cleanly;
            // for websites/stores supply a "scope_code" in the entry.
            if ($scope === 'default' || $scope === 'defaults') {
                $scopeKey = 'default';
            } elseif (!empty($entry['scope_code'])) {
                $scopeKey = $scope . '/' . $entry['scope_code'];
            } else {
                // Non-default scope without a code cannot be expressed as a static file value.
                continue;
            }
            $fullPath = 'system/' . $scopeKey . '/' . $entry['path'];
            $this->setByPath($tree, $fullPath, $entry['value'] ?? '');
        }
        return $tree;
    }

    private function setByPath(array &$tree, string $path, $value): void
    {
        $segments = explode('/', $path);
        $node = &$tree;
        foreach ($segments as $segment) {
            if (!isset($node[$segment]) || !is_array($node[$segment])) {
                $node[$segment] = [];
            }
            $node = &$node[$segment];
        }
        $node = $value;
    }

    private function countLeaves(array $data): int
    {
        $count = 0;
        foreach ($data as $value) {
            $count += is_array($value) ? $this->countLeaves($value) : 1;
        }
        return $count;
    }
}
