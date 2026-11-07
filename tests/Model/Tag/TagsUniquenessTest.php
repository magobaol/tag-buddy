<?php

namespace Tests\Model\Tag;

use Common\Exception\TagNameAlreadyUsed;
use Common\Exception\TagNotFound;
use Model\Tag\Tag;
use Model\Tag\Tags;
use PHPUnit\Framework\TestCase;

class TagsUniquenessTest extends TestCase
{
    private function tagsWithCoding(): Tags
    {
        return Tags::fromArrayOfTag([
            Tag::fromArray(['name' => 'ai-coding', 'aliases' => ['coding', 'vibe coding']]),
            Tag::fromString('automazione'),
        ]);
    }

    /**
     * @test
     */
    function it_should_refuse_a_name_already_used_regardless_of_case()
    {
        $tags = $this->tagsWithCoding();

        $this->expectException(TagNameAlreadyUsed::class);

        $tags->add(Tag::fromString('AI-Coding'));
    }

    /**
     * @test
     */
    function it_should_refuse_a_name_equal_to_an_alias_of_another_tag()
    {
        $tags = $this->tagsWithCoding();

        $this->expectException(TagNameAlreadyUsed::class);

        $tags->add(Tag::fromString('coding'));
    }

    /**
     * @test
     */
    function it_should_refuse_an_alias_equal_to_the_name_of_another_tag()
    {
        $tags = $this->tagsWithCoding();

        $this->expectException(TagNameAlreadyUsed::class);

        $tags->add(Tag::fromArray(['name' => 'new-tag', 'aliases' => ['automazione']]));
    }

    /**
     * @test
     */
    function it_should_refuse_an_alias_equal_to_an_alias_of_another_tag()
    {
        $tags = $this->tagsWithCoding();

        $this->expectException(TagNameAlreadyUsed::class);

        $tags->add(Tag::fromArray(['name' => 'new-tag', 'aliases' => ['Vibe Coding']]));
    }

    /**
     * @test
     */
    function it_should_say_which_value_collides_and_with_which_tag()
    {
        $tags = $this->tagsWithCoding();

        try {
            $tags->add(Tag::fromArray(['name' => 'new-tag', 'aliases' => ['vibe coding']]));
            $this->fail('Expected TagNameAlreadyUsed');
        } catch (TagNameAlreadyUsed $e) {
            $this->assertEquals(['value' => 'vibe coding', 'usedBy' => 'ai-coding'], $e->getDetails());
        }
    }

    /**
     * @test
     */
    function it_should_add_a_tag_that_collides_with_nothing()
    {
        $tags = $this->tagsWithCoding();

        $tags->add(Tag::fromArray(['name' => 'new-tag', 'aliases' => ['fresh']]));

        $this->assertCount(3, $tags->getTags());
    }

    /**
     * @test
     */
    function it_should_still_load_a_file_that_already_contains_collisions()
    {
        $file = sys_get_temp_dir().'/tag-buddy-collisions-'.uniqid().'.yaml';
        file_put_contents($file, "tags:\n  a:\n    name: a\n    aliases: [b]\n  b:\n    name: b\n");

        try {
            $tags = Tags::fromYamlFile($file);
        } finally {
            unlink($file);
        }

        $this->assertCount(2, $tags->getTags());
    }

    /**
     * @test
     */
    function it_should_replace_a_tag_keeping_its_position()
    {
        $tags = $this->tagsWithCoding();

        $tags->replace(Tag::fromArray(['name' => 'ai-coding', 'aliases' => ['coding'], 'description' => 'Desc']));

        $this->assertEquals('ai-coding', $tags->getTags()[0]->getName());
        $this->assertEquals('Desc', $tags->getTags()[0]->getDescription());
        $this->assertCount(2, $tags->getTags());
    }

    /**
     * @test
     */
    function it_should_refuse_a_replacement_whose_alias_collides_with_another_tag()
    {
        $tags = $this->tagsWithCoding();

        $this->expectException(TagNameAlreadyUsed::class);

        $tags->replace(Tag::fromArray(['name' => 'ai-coding', 'aliases' => ['automazione']]));
    }

    /**
     * @test
     */
    function it_should_fail_replacing_a_tag_that_does_not_exist()
    {
        $tags = $this->tagsWithCoding();

        $this->expectException(TagNotFound::class);

        $tags->replace(Tag::fromString('unknown'));
    }
}
