<?php
namespace Forever\DemoInstaller\Console\Command;

use Forever\DemoInstaller\Model\DemoManager;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DemoImportCommand extends Command
{
    public function __construct(
        private DemoManager $demoManager,
        private State $appState,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('forever:demo:import')
            ->setDescription('Import a Forever demo package')
            ->addArgument('code', InputArgument::REQUIRED, 'Demo code (e.g. furniture)')
            ->addOption('store', 's', InputOption::VALUE_REQUIRED, 'Target store view id', '0')
            ->addOption('types', 't', InputOption::VALUE_REQUIRED, 'Comma list of step types (default: all)')
            ->addOption('no-media', null, InputOption::VALUE_NONE, 'Skip media copy')
            ->addOption('no-overwrite', null, InputOption::VALUE_NONE, 'Do not overwrite existing records')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Do not ask for confirmation');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        } catch (\Throwable $e) {
            // area code already set
        }

        $code = $input->getArgument('code');
        if (!$input->getOption('force')) {
            $helper = $this->getHelper('question');
            $q = new ConfirmationQuestion(
                "<question>This will create/overwrite store data with demo \"$code\". Continue? [y/N]</question> ",
                false
            );
            if (!$helper->ask($input, $output, $q)) {
                $output->writeln('<comment>Aborted.</comment>');
                return Command::SUCCESS;
            }
        }

        $types = array_filter(array_map('trim', explode(',', (string)$input->getOption('types'))));

        $context = $this->demoManager->import(
            $code,
            (int)$input->getOption('store'),
            $types,
            !$input->getOption('no-overwrite'),
            !$input->getOption('no-media')
        );

        foreach ($context->getMessages() as $msg) {
            $output->writeln($msg);
        }
        $output->writeln('<info>Demo import finished. Run: bin/magento cache:flush</info>');
        return Command::SUCCESS;
    }
}
