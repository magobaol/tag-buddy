<?php

namespace App\Command;

use App\TagsSearch\OutputFormat;
use App\TagsSearch\TagsSearchFactory;
use Model\Tag\Tags;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:tags:search',
    description: 'Add a short description for your command',
)]
class TagsSearchCommand extends Command
{
    private string $tagsFilePath;

    public function __construct(string $tagsFilePath)
    {
        parent::__construct();
        $this->tagsFilePath = $tagsFilePath;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('search-terms', InputArgument::OPTIONAL, 'The string you want to search. If omitted the whole tag list is returned')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $search = $input->getArgument('search-terms') ?? "";

        if (!$input->getOption('output') || !OutputFormat::isValid($input->getOption('output'))) {
            $outputFormat = OutputFormat::getDefaultFormat();
        } else {
            $outputFormat = OutputFormat::fromString($input->getOption('output'));
        }

        $tagsSearch = TagsSearchFactory::make($this->tagsFilePath, $outputFormat);
        $output->write($tagsSearch->search($search));

        return Command::SUCCESS;
    }
}
