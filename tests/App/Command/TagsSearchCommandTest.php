<?php

namespace Tests\App\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class TagsSearchCommandTest extends KernelTestCase
{
    public function test_execute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:tags:search');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--output' => 'list',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('OKR', $output);

    }
}