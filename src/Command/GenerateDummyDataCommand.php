<?php

namespace App\Command;

use App\Service\PropertyService;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'app:generate-dummy-data',
    description: 'Generates dummy property data in JSON format and clears the properties cache.',
)]
class GenerateDummyDataCommand extends Command
{
    protected static $defaultName = 'app:generate-dummy-data';
    private string $projectDir;
    private PropertyService $propertyService;

    public function __construct(string $projectDir, PropertyService $propertyService)
    {
        parent::__construct();
        $this->projectDir = $projectDir;
        $this->propertyService = $propertyService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('records', null, InputOption::VALUE_OPTIONAL, 'Number of records to generate', 15)
            ->addOption('source', null, InputOption::VALUE_OPTIONAL, 'Data source (e.g., Source1, Source2)', 'Source1')
            ->addOption('output', null, InputOption::VALUE_OPTIONAL, 'Output file name', 'dummy_data.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $records = (int) $input->getOption('records');
        $source = $input->getOption('source');
        $outputFile = $this->projectDir . '/public/data/' . $input->getOption('output');

        $faker = Factory::create();
        $data = [];

        for ($i = 1; $i <= $records; $i++) {
            $data[] = [
                'id'      => $i,
                'address' => $faker->streetAddress,
                'price'   => $faker->numberBetween(100000, 500000),
                'source'  => $source,
            ];
        }
        $filesystem = new Filesystem();
        $filesystem->mkdir(dirname($outputFile));

        file_put_contents($outputFile, json_encode($data, JSON_PRETTY_PRINT));
        $output->writeln("Generated {$records} dummy property records in {$outputFile}");

        $this->propertyService->clearCache();
        $output->writeln("Properties cache cleared.");

        return Command::SUCCESS;
    }
}
