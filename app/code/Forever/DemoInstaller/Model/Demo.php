<?php
namespace Forever\DemoInstaller\Model;

class Demo
{
    public function __construct(
        private string $code,
        private string $label,
        private string $version,
        private string $path,
        private array  $manifest
    ) {}

    public function getCode(): string { return $this->code; }
    public function getLabel(): string { return $this->label; }
    public function getVersion(): string { return $this->version; }
    public function getPath(): string { return $this->path; }
    public function getManifest(): array { return $this->manifest; }
    public function getSteps(): array { return $this->manifest['steps'] ?? []; }
    public function getThumbnail(): ?string { return $this->manifest['thumbnail'] ?? null; }
    public function getDescription(): string { return (string)($this->manifest['description'] ?? ''); }
}
