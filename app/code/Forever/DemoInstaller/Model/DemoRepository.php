<?php
namespace Forever\DemoInstaller\Model;

use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Exception\LocalizedException;

class DemoRepository
{
    private const MODULE = 'Forever_DemoInstaller';

    public function __construct(
        private ModuleDirReader $moduleDirReader,
        private FileDriver $file,
        private Json $json
    ) {}

    public function getBaseDir(): string
    {
        return $this->moduleDirReader->getModuleDir('', self::MODULE) . '/data/demo';
    }

    /** @return Demo[] keyed by code */
    public function getList(): array
    {
        $base = $this->getBaseDir();
        $demos = [];
        if (!$this->file->isDirectory($base)) {
            return $demos;
        }
        foreach ($this->file->readDirectory($base) as $dir) {
            $manifestPath = $dir . '/manifest.json';
            if (!$this->file->isFile($manifestPath)) {
                continue;
            }
            $manifest = $this->json->unserialize($this->file->fileGetContents($manifestPath));
            $code = $manifest['code'] ?? basename($dir);
            $demos[$code] = new Demo(
                $code,
                (string)($manifest['label'] ?? $code),
                (string)($manifest['version'] ?? '1.0.0'),
                $dir,
                $manifest
            );
        }
        ksort($demos);
        return $demos;
    }

    public function get(string $code): Demo
    {
        $list = $this->getList();
        if (!isset($list[$code])) {
            throw new LocalizedException(__('Demo package "%1" was not found.', $code));
        }
        return $list[$code];
    }
}
