<?php

namespace App\Command;

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
        $tagSearch = new TagSearch($this->tagsFilePath);
        $search = $input->getArgument('search-terms') ?? "";

        if (!$input->getOption('output') || !in_array($input->getOption('output'), TagSearch::getOutputFormats())) {
            $outputFormat = TagSearch::getDefaultOutputFormat();
        } else {
            $outputFormat = $input->getOption('output');
        }

        $output->write($tagSearch->getResult($search, $outputFormat));
        return Command::SUCCESS;
    }
}