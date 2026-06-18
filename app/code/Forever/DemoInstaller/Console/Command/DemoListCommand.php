<?php
namespace Forever\DemoInstaller\Console\Command;

use Forever\DemoInstaller\Model\DemoRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DemoListCommand extends Command
{
    public function __construct(private DemoRepository $demoRepository, ?string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('forever:demo:list')->setDescription('List available Forever demo packages');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $demos = $this->demoRepository->getList();
        if (!$demos) {
            $output->writeln('<comment>No demo packages found under ' . $this->demoRepository->getBaseDir() . '</comment>');
            return Command::SUCCESS;
        }
        $output->writeln('<info>Available demos:</info>');
        foreach ($demos as $demo) {
            $output->writeln(sprintf('  <info>%-14s</info> %s (v%s)', $demo->getCode(), $demo->getLabel(), $demo->getVersion()));
        }
        return Command::SUCCESS;
    }
}
