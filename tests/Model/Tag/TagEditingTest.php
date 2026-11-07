<?php

namespace Tests\Model\Tag;

use Common\Exception\AliasNotFound;
use Common\Exception\TagNameAlreadyUsed;
use Model\Tag\Tag;
use PHPUnit\Framework\TestCase;

class TagEditingTest extends TestCase
{
    /**
     * @test
     */
    function it_should_have_no_description_by_default()
    {
        $this->assertNull(Tag::fromString('tag-name')->getDescription());
        $this->assertNull(Tag::fromArray(['name' => 'tag-name'])->getDescription());
    }

    /**
     * @test
     */
    function it_should_read_the_description_from_array()
    {
        $tag = Tag::fromArray(['name' => 'ai-coding', 'description' => 'Writing code with an assistant']);

        $this->assertEquals('Writing code with an assistant', $tag->getDescription());
    }

    /**
     * @test
     */
    function it_should_not_write_the_description_key_when_there_is_none()
    {
        $this->assertArrayNotHasKey('description', Tag::fromString('tag-name')->toArray());
        $this->assertArrayNotHasKey(
            'description',
            Tag::fromArray(['name' => 'tag-name', 'description' => ''])->toArray()
        );
    }

    /**
     * @test
     */
    function it_should_write_the_description_when_there_is_one()
    {
        $tag = Tag::fromArray(['name' => 'ai-coding', 'description' => 'Writing code with an assistant']);

        $this->assertEquals('Writing code with an assistant', $tag->toArray()['description']);
    }

    /**
     * @test
     */
    function it_should_keep_the_description_when_cloned()
    {
        $tag = Tag::fromArray(['name' => 'ai-coding', 'description' => 'Writing code with an assistant']);

        $this->assertEquals('Writing code with an assistant', $tag->clone()->getDescription());
    }

    /**
     * @test
     */
    function it_should_return_a_copy_with_a_new_description_leaving_the_original_untouched()
    {
        $tag = Tag::fromString('ai-coding');

        $described = $tag->withDescription('  Writing code with an assistant ');

        $this->assertEquals('Writing code with an assistant', $described->getDescription());
        $this->assertNull($tag->getDescription());
    }

    /**
     * @test
     */
    function it_should_clear_the_description_with_an_empty_string()
    {
        $tag = Tag::fromArray(['name' => 'ai-coding', 'description' => 'Writing code with an assistant']);

        $cleared = $tag->withDescription('');

        $this->assertNull($cleared->getDescription());
        $this->assertArrayNotHasKey('description', $cleared->toArray());
    }

    /**
     * @test
     */
    function it_should_return_a_copy_with_an_added_alias_keeping_name_and_description()
    {
        $tag = Tag::fromArray(['name' => 'ai-coding', 'aliases' => ['coding'], 'description' => 'Desc']);

        $updated = $tag->withAddedAlias('vibe coding');

        $this->assertEquals(['coding', 'vibe coding'], $updated->getAliasesAsArray());
        $this->assertEquals('ai-coding', $updated->getName());
        $this->assertEquals('Desc', $updated->getDescription());
        $this->assertEquals(['coding'], $tag->getAliasesAsArray());
    }

    /**
     * @test
     */
    function it_should_refuse_to_add_an_alias_the_tag_already_has_regardless_of_case()
    {
        $tag = Tag::fromArray(['name' => 'ai-coding', 'aliases' => ['Coding']]);

        $this->expectException(TagNameAlreadyUsed::class);

        $tag->withAddedAlias('coding');
    }

    /**
     * @test
     */
    function it_should_return_a_copy_without_an_alias()
    {
        $tag = Tag::fromArray(['name' => 'ai-coding', 'aliases' => ['coding', 'vibe coding']]);

        $updated = $tag->withoutAlias('Coding');

        $this->assertEquals(['vibe coding'], $updated->getAliasesAsArray());
        $this->assertEquals(['coding', 'vibe coding'], $tag->getAliasesAsArray());
    }

    /**
     * @test
     */
    function it_should_fail_when_removing_an_alias_the_tag_does_not_have()
    {
        $tag = Tag::fromArray(['name' => 'ai-coding', 'aliases' => ['coding']]);

        $this->expectException(AliasNotFound::class);

        $tag->withoutAlias('other');
    }

    /**
     * @test
     */
    function it_should_list_its_name_and_aliases()
    {
        $tag = Tag::fromArray(['name' => 'ai-coding', 'aliases' => ['coding', 'vibe coding']]);

        $this->assertEquals(['ai-coding', 'coding', 'vibe coding'], $tag->getNameAndAliases());
    }
}
