<?php
namespace Forever\DemoInstaller\Api;

use Forever\DemoInstaller\Model\ImportContext;

interface ImporterInterface
{
    /**
     * Apply one manifest step to the store.
     *
     * @param array         $step    The step node from manifest.json
     * @param ImportContext $context Shared run context (package path, store, flags, log)
     * @return void
     */
    public function import(array $step, ImportContext $context): void;
}
