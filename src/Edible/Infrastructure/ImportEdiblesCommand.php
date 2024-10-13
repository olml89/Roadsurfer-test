<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure;

use App\Edible\Domain\Importer;
use JsonException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:import-edibles',
    description: 'Import edibles from a json file.',
    hidden: false,
)]
final class ImportEdiblesCommand extends Command
{
    private readonly Importer $importer;

    public function __construct(Importer $importer)
    {
        $this->importer = $importer;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED);
    }

    /**
     * @throws JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $file */
        $file = $input->getArgument('file');

        if (!file_exists($file)) {
            $output->writeln('<error>' . sprintf('The file "%s" does not exist', $file) . '</error>');

            return Command::INVALID;
        }

        $count = $this->importer->import($file);
        $message = sprintf('%s edibles have been successfully imported from "%s"%s', $count, $file, PHP_EOL);
        $output->writeln('<info>' . $message . '</info>');

        return Command::SUCCESS;
    }
}