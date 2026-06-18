<?php
namespace Forever\DemoInstaller\Model;

class ExportContext
{
    private string $packagePath = '';
    private int $storeId = 0;
    /** @var string[] */
    private array $messages = [];

    public function setPackagePath(string $path): self { $this->packagePath = rtrim($path, '/'); return $this; }
    public function getPackagePath(): string { return $this->packagePath; }

    public function setStoreId(int $storeId): self { $this->storeId = $storeId; return $this; }
    public function getStoreId(): int { return $this->storeId; }

    public function addMessage(string $m): self { $this->messages[] = $m; return $this; }
    /** @return string[] */
    public function getMessages(): array { return $this->messages; }

    public function file(string $relative): string { return $this->packagePath . '/' . ltrim($relative, '/'); }
}
