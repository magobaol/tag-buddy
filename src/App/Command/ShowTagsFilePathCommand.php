<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:tags:show-file-path',
    description: 'Add a short description for your command',
)]
class ShowTagsFilePathCommand extends Command
{
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        parent::__construct();
        $this->parameterBag = $parameterBag;
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userDefinedFilePath = $this->parameterBag->get('app.tags_file_path');
        if (str_starts_with($userDefinedFilePath, '/')) {
            $filePath = $userDefinedFilePath;
        } else {
            $filePath = getcwd().'/'.$userDefinedFilePath;
        }
        $output->writeln($filePath);
        return Command::SUCCESS;
    }

}
