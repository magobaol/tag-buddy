<?php

namespace Tests\App\Command;

use App\Command\TagsAddCommand;
use Model\Tag\Tags;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class TagsAddCommandTest extends TestCase
{
    private string $tagsFile;

    protected function setUp(): void
    {
        $this->tagsFile = sys_get_temp_dir().'/tag-buddy-add-'.uniqid().'.yaml';
        copy(dirname(__DIR__, 2).'/Fixtures/tags-for-commands.yaml', $this->tagsFile);
    }

    protected function tearDown(): void
    {
        @unlink($this->tagsFile);
    }

    /**
     * @test
     */
    function it_should_add_a_tag()
    {
        $tester = $this->execute(['name' => 'new-tag']);

        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
        $this->assertNotNull(Tags::fromYamlFile($this->tagsFile)->findByName(\Model\Tag\Name::fromString('new-tag')));
    }

    /**
     * @test
     */
    function it_should_add_a_tag_with_a_description()
    {
        $this->execute(['name' => 'new-tag', '--description' => 'What it covers']);

        $tag = Tags::fromYamlFile($this->tagsFile)->findByName(\Model\Tag\Name::fromString('new-tag'));
        $this->assertEquals('What it covers', $tag->getDescription());
    }

    /**
     * @test
     */
    function it_should_refuse_a_name_already_used_and_leave_the_file_untouched()
    {
        $before = file_get_contents($this->tagsFile);

        $tester = $this->execute(['name' => 'OKR']);

        $this->assertEquals(Command::FAILURE, $tester->getStatusCode());
        $this->assertStringContainsString('OKR', $tester->getDisplay());
        $this->assertSame($before, file_get_contents($this->tagsFile));
    }

    /**
     * @test
     */
    function it_should_refuse_a_name_equal_to_an_alias_of_another_tag()
    {
        $before = file_get_contents($this->tagsFile);

        $tester = $this->execute(['name' => 'analogy']);

        $this->assertEquals(Command::FAILURE, $tester->getStatusCode());
        $this->assertStringContainsString('analogia', $tester->getDisplay());
        $this->assertSame($before, file_get_contents($this->tagsFile));
    }

    private function execute(array $input): CommandTester
    {
        $tester = new CommandTester(new TagsAddCommand($this->tagsFile));
        $tester->execute($input);

        return $tester;
    }
}
