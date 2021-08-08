<?php

namespace App\Command;

use App\Model\Tag\Tag;
use App\Model\Tag\Tags;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tagName = $input->getArgument('name');

        $tag = Tag::fromString($tagName);

        $tags = Tags::fromYamlFile($this->tagsFilePath);
        $tags->add($tag);

        $yamlData = [
            'tags' => $tags->toArrayWithNamesAsKeys()
        ];

        $yamlContent = Yaml::dump($yamlData, 4, 2);

        file_put_contents($this->tagsFilePath, $yamlContent);

        return Command::SUCCESS;
    }
}
