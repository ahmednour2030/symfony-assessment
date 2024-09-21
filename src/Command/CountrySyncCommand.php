<?php
declare(strict_types=1);

namespace App\Command;

use App\Services\CountrySyncService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

// php bin/console countries:sync --batchSize=50
class CountrySyncCommand extends Command
{
    /**
     * @var CountrySyncService
     */
    private CountrySyncService $countrySyncService;

    /**
     * @param  CountrySyncService  $countrySyncService
     */
    public function __construct(CountrySyncService $countrySyncService)
    {
        $this->countrySyncService = $countrySyncService;
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('countries:sync');
        $this->setDescription('Synchronize the countries');
        $this->addOption(
            'batchSize',
            null,
            InputOption::VALUE_OPTIONAL,
            'Number of countries to sync per batch',
            30
        );
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface  $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $batchSize = $input->getOption('batchSize');

        $io->info(sprintf('Starting country sync with batch size: %d', $batchSize));

        try {
            $this->countrySyncService->syncCountries((int)$batchSize);
            $io->success('Countries have been successfully synced.');
        } catch (\Exception $e) {
            $io->error('Error occurred while syncing countries: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}