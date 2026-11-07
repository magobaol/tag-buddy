<?php

namespace Tests\App\Command;

use App\Command\TagsEditCommand;
use Model\Tag\Name;
use Model\Tag\Tag;
use Model\Tag\Tags;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class TagsEditCommandTest extends TestCase
{
    private string $tagsFile;

    protected function setUp(): void
    {
        $this->tagsFile = sys_get_temp_dir().'/tag-buddy-edit-'.uniqid().'.yaml';
        copy(dirname(__DIR__, 2).'/Fixtures/tags-for-commands.yaml', $this->tagsFile);
    }

    protected function tearDown(): void
    {
        @unlink($this->tagsFile);
    }

    /**
     * @test
     */
    function it_should_set_the_description_of_an_existing_tag()
    {
        $tester = $this->execute(['name' => 'analogia', '--description' => 'Comparing two things']);

        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
        $this->assertEquals('Comparing two things', $this->tag('analogia')->getDescription());
        $this->assertEquals(['analogy'], $this->tag('analogia')->getAliasesAsArray());
    }

    /**
     * @test
     */
    function it_should_change_an_existing_description()
    {
        $this->execute(['name' => 'analogia', '--description' => 'First']);
        $this->execute(['name' => 'analogia', '--description' => 'Second']);

        $this->assertEquals('Second', $this->tag('analogia')->getDescription());
    }

    /**
     * @test
     */
    function it_should_clear_the_description_with_an_empty_value_and_remove_the_key()
    {
        $this->execute(['name' => 'analogia', '--description' => 'First']);

        $tester = $this->execute(['name' => 'analogia', '--description' => '']);

        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
        $this->assertNull($this->tag('analogia')->getDescription());
        $this->assertStringNotContainsString('description', file_get_contents($this->tagsFile));
    }

    /**
     * @test
     */
    function it_should_add_aliases_one_at_a_time()
    {
        $tester = $this->execute(['name' => 'analogia', '--add-alias' => ['similitude', 'parallel']]);

        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
        $this->assertEquals(['analogy', 'similitude', 'parallel'], $this->tag('analogia')->getAliasesAsArray());
    }

    /**
     * @test
     */
    function it_should_remove_aliases_one_at_a_time()
    {
        $tester = $this->execute(['name' => 'apprendimento', '--remove-alias' => ['learning']]);

        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
        $this->assertEquals(['formazione'], $this->tag('apprendimento')->getAliasesAsArray());
    }

    /**
     * @test
     */
    function it_should_find_the_tag_regardless_of_case()
    {
        $tester = $this->execute(['name' => 'okr', '--description' => 'Goals framework']);

        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
        $this->assertEquals('Goals framework', $this->tag('OKR')->getDescription());
    }

    /**
     * @test
     */
    function it_should_fail_for_a_tag_that_does_not_exist_and_leave_the_file_untouched()
    {
        $before = file_get_contents($this->tagsFile);

        $tester = $this->execute(['name' => 'unknown', '--description' => 'x']);

        $this->assertEquals(Command::FAILURE, $tester->getStatusCode());
        $this->assertStringContainsString('unknown', $tester->getDisplay());
        $this->assertSame($before, file_get_contents($this->tagsFile));
    }

    /**
     * @test
     */
    function it_should_refuse_an_alias_used_by_another_tag_and_leave_the_file_untouched()
    {
        $before = file_get_contents($this->tagsFile);

        $tester = $this->execute(['name' => 'analogia', '--add-alias' => ['automation']]);

        $this->assertEquals(Command::FAILURE, $tester->getStatusCode());
        $this->assertStringContainsString('automazione', $tester->getDisplay());
        $this->assertSame($before, file_get_contents($this->tagsFile));
    }

    /**
     * @test
     */
    function it_should_refuse_an_alias_equal_to_the_name_of_another_tag()
    {
        $tester = $this->execute(['name' => 'analogia', '--add-alias' => ['software']]);

        $this->assertEquals(Command::FAILURE, $tester->getStatusCode());
    }

    /**
     * @test
     */
    function it_should_fail_removing_an_alias_the_tag_does_not_have_and_leave_the_file_untouched()
    {
        $before = file_get_contents($this->tagsFile);

        $tester = $this->execute(['name' => 'analogia', '--remove-alias' => ['nope']]);

        $this->assertEquals(Command::FAILURE, $tester->getStatusCode());
        $this->assertStringContainsString('nope', $tester->getDisplay());
        $this->assertSame($before, file_get_contents($this->tagsFile));
    }

    /**
     * @test
     */
    function it_should_fail_when_there_is_nothing_to_change()
    {
        $before = file_get_contents($this->tagsFile);

        $tester = $this->execute(['name' => 'analogia']);

        $this->assertEquals(Command::FAILURE, $tester->getStatusCode());
        $this->assertSame($before, file_get_contents($this->tagsFile));
    }

    private function execute(array $input): CommandTester
    {
        $tester = new CommandTester(new TagsEditCommand($this->tagsFile));
        $tester->execute($input);

        return $tester;
    }

    private function tag(string $name): Tag
    {
        return Tags::fromYamlFile($this->tagsFile)->findByName(Name::fromString($name));
    }
}
