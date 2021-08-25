<?php

namespace Tests\Model\Tag;

use Common\Exception\AssertionFailed;
use Model\Tag\Tag;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    /**
     * @test
     */
    function it_should_create_from_array()
    {
        $array = [
            'name' => 'tag-name',
            'aliases' => [
                'tag-name-alt-1',
                'tag-name-alt-2'
            ]
        ];

        $tag = Tag::fromArray($array);

        $this->assertEquals('tag-name', $tag->getName());
        $this->assertCount(2, $tag->getAliasesAsArray());
    }

    /**
     * @test
     */
    function it_should_create_from_string()
    {
        $tag = Tag::fromString('tag-name');

        $this->assertEquals('tag-name', $tag->getName());
        $this->assertCount(0, $tag->getAliasesAsArray());
    }

    /**
     * @test
     */
    function it_should_fail_without_name()
    {
        $this->expectException(AssertionFailed::class);
        $tag = Tag::fromArray([]);
    }

    /**
     * @test
     */
    function it_should_not_fail_without_aliases()
    {
        $tag = Tag::fromArray(['name' => 'tag-name']);

        $this->assertEquals('tag-name', $tag->getName());
    }

    /**
     * @test
     */
    function it_should_not_fail_if_aliases_are_empty()
    {
        $tag = Tag::fromArray(['name' => 'tag', 'aliases' => []]);
        $this->assertTrue($tag instanceof Tag);
    }

    /**
     * @test
     */
    function it_should_return_an_array()
    {
        $array = [
            'name' => 'tag-name',
            'aliases' => [
                'tag-name-alt-1',
                'tag-name-alt-2'
            ]
        ];

        $tag = Tag::fromArray($array);

        $tagArray = $tag->toArray();

        $this->assertCount(2, $tagArray);
        $this->assertEquals('tag-name', $tagArray['name']);
        $this->assertCount(2, $tagArray['aliases']);
        $this->assertEquals('tag-name-alt-1', $tagArray['aliases'][0]);
        $this->assertEquals('tag-name-alt-2', $tagArray['aliases'][1]);
    }

    /**
     * @test
     */
    function it_should_return_all_aliases_as_one_string()
    {
        $array = [
            'name' => 'tag-name',
            'aliases' => [
                'tag-name-alt-1',
                'tag-name-alt-2'
            ]
        ];

        $tag = Tag::fromArray($array);

        $this->assertEquals('tag-name-alt-1; tag-name-alt-2', $tag->getAliasesAsOneString());
    }

    /**
     * @test
     */
    function it_should_return_searchable_lowered_string()
    {
        $array = [
            'name' => 'tag-NAME',
            'aliases' => [
                'tag-name-ALT-1',
                'tag-name-alt-2'
            ]
        ];

        $tag = Tag::fromArray($array);

        $this->assertEquals('tag-name; tag-name-alt-1; tag-name-alt-2', $tag->getSearchableLoweredString());
    }

    /**
     * @test
     */
    function it_should_clone_itself()
    {
        $array = [
            'name' => 'tag-NAME',
            'aliases' => [
                'tag-name-ALT-1',
                'tag-name-alt-2'
            ]
        ];

        $tag = Tag::fromArray($array);

        $cloned = $tag->clone();

        $this->assertEquals($tag->getName(), $cloned->getName());
        $this->assertEquals($tag->getAliasesAsArray(), $cloned->getAliasesAsArray());
    }
}
