<?php
namespace Forever\DemoInstaller\Api;

use Forever\DemoInstaller\Model\ExportContext;

interface ExporterInterface
{
    /**
     * Write data of this step type from the live store into the package folder.
     *
     * @param array         $step
     * @param ExportContext $context
     * @return void
     */
    public function export(array $step, ExportContext $context): void;
}
