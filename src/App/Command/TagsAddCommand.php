<?php

namespace App\Command;

use Common\Exception\TagNameAlreadyUsed;
use Model\Tag\Tag;
use Model\Tag\Tags;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:tags:add',
    description: 'Add a short description for your command',
)]
class TagsAddCommand extends Command
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
            ->addArgument('name', InputArgument::OPTIONAL, 'The tag name')
            ->addOption('description', null, InputOption::VALUE_REQUIRED, 'Cosa copre il tag')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tagName = $input->getArgument('name');

        $tag = Tag::fromString($tagName)->withDescription($input->getOption('description'));

        $tags = Tags::fromYamlFile($this->tagsFilePath);
        try {
            $tags->add($tag);
        } catch (TagNameAlreadyUsed $e) {
            $errors = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
            $errors->writeln(TagErrorMessage::for($e));

            return Command::FAILURE;
        }
        $tags->toYamlFile($this->tagsFilePath);

        return Command::SUCCESS;
    }
}
