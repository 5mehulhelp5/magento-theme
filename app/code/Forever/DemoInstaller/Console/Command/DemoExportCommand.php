<?php
namespace Forever\DemoInstaller\Console\Command;

use Forever\DemoInstaller\Model\ExportManager;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DemoExportCommand extends Command
{
    public function __construct(
        private ExportManager $exportManager,
        private State $appState,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('forever:demo:export')
            ->setDescription('Export current store data into a demo package (manifest must already exist)')
            ->addArgument('code', InputArgument::REQUIRED, 'Demo code (folder under data/demo)')
            ->addOption('store', 's', InputOption::VALUE_REQUIRED, 'Source store view id', '0')
            ->addOption('types', 't', InputOption::VALUE_REQUIRED, 'Comma list of step types');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        } catch (\Throwable $e) {
            // area code already set
        }

        $types = array_filter(array_map('trim', explode(',', (string)$input->getOption('types'))));
        $context = $this->exportManager->export(
            $input->getArgument('code'),
            (int)$input->getOption('store'),
            $types
        );
        foreach ($context->getMessages() as $msg) {
            $output->writeln($msg);
        }
        return Command::SUCCESS;
    }
}
