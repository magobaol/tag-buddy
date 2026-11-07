<?php

namespace App\Command;

use Common\Exception\TagBuddyException;
use Common\Exception\TagNotFound;
use Model\Tag\Name;
use Model\Tag\Tags;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:tags:edit',
    description: 'Modifica la descrizione e gli alias di un tag esistente',
)]
class TagsEditCommand extends Command
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
            ->addArgument('name', InputArgument::REQUIRED, 'Il nome del tag')
            ->addOption('description', null, InputOption::VALUE_REQUIRED, 'Imposta la descrizione; vuota la cancella')
            ->addOption('add-alias', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Aggiunge un alias (ripetibile)')
            ->addOption('remove-alias', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Toglie un alias (ripetibile)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $description = $input->getOption('description');
        $aliasesToAdd = $input->getOption('add-alias');
        $aliasesToRemove = $input->getOption('remove-alias');

        if ($description === null && !$aliasesToAdd && !$aliasesToRemove) {
            return $this->fail($output, 'Nessuna modifica richiesta');
        }

        $tags = Tags::fromYamlFile($this->tagsFilePath);
        $name = $input->getArgument('name');

        try {
            $tag = $tags->findByName(Name::fromString($name)) ?? throw TagNotFound::forName($name);

            if ($description !== null) {
                $tag = $tag->withDescription($description);
            }
            foreach ($aliasesToRemove as $alias) {
                $tag = $tag->withoutAlias($alias);
            }
            foreach ($aliasesToAdd as $alias) {
                $tag = $tag->withAddedAlias($alias);
            }

            $tags->replace($tag);
        } catch (TagBuddyException $e) {
            return $this->fail($output, TagErrorMessage::for($e));
        }
        $tags->toYamlFile($this->tagsFilePath);

        return Command::SUCCESS;
    }

    private function fail(OutputInterface $output, string $message): int
    {
        $errors = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        $errors->writeln($message);

        return Command::FAILURE;
    }
}
