<?php
namespace Forever\DemoInstaller\Model;

class ImportContext
{
    private string $packagePath = '';
    private int $storeId = 0;
    private bool $overwrite = true;
    private bool $importMedia = true;
    /** @var string[] */
    private array $messages = [];

    public function setPackagePath(string $path): self { $this->packagePath = rtrim($path, '/'); return $this; }
    public function getPackagePath(): string { return $this->packagePath; }

    public function setStoreId(int $storeId): self { $this->storeId = $storeId; return $this; }
    public function getStoreId(): int { return $this->storeId; }

    public function setOverwrite(bool $v): self { $this->overwrite = $v; return $this; }
    public function isOverwrite(): bool { return $this->overwrite; }

    public function setImportMedia(bool $v): self { $this->importMedia = $v; return $this; }
    public function isImportMedia(): bool { return $this->importMedia; }

    public function addMessage(string $message): self { $this->messages[] = $message; return $this; }
    /** @return string[] */
    public function getMessages(): array { return $this->messages; }

    public function file(string $relative): string { return $this->packagePath . '/' . ltrim($relative, '/'); }
}
