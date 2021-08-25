<?php

namespace Tests\Model\Tag;

use Model\Tag\Name;
use Model\Tag\Tag;
use Model\Tag\Tags;
use PHPUnit\Framework\TestCase;
use Tests\Model\Tag\Builder\TagBuilder;

class TagsTest extends TestCase
{
    private string $notExistingTagsFile;
    private string $tagsFile;

    protected function setUp(): void
    {
        $this->tagsFile = dirname(__FILE__) . '/tags.yaml';
        $this->notExistingTagsFile = dirname(__FILE__).'/not-existing-tags-file.yaml';
    }

    public function tearDown(): void
    {
        @unlink($this->notExistingTagsFile);
    }

    /**
     * @test
     */
    function it_should_load_an_empty_list_if_yaml_does_not_exists()
    {
        $tags = Tags::fromYamlFile($this->notExistingTagsFile);

        $tagsAsArray = $tags->toArray();
        $this->assertCount(0, $tagsAsArray);
    }

    /**
     * @test
     */
    function it_should_save_tags_to_file()
    {
        $filename = $this->notExistingTagsFile;

        $this->assertFalse(file_exists($filename));

        $tags = Tags::fromYamlFile($filename);

        $tags->add(Tag::fromString('new-tag'));

        $tags->toYamlFile($filename);

        $this->assertTrue(file_exists($filename));
    }

    /**
     * @test
     */
    function it_should_load_tags_from_yaml_file()
    {
        $tags = Tags::fromYamlFile($this->tagsFile);

        $tagsAsArray = $tags->toArray();
        $this->assertCount(8, $tagsAsArray);

        $this->assertEquals('OKR', $tagsAsArray[0]['name']);
        $this->assertCount(2, $tagsAsArray[0]['aliases']);
        $this->assertEquals('objectives and key results', $tagsAsArray[0]['aliases'][0]);
        $this->assertEquals('obiettivi e risultati misurabili', $tagsAsArray[0]['aliases'][1]);

        $this->assertEquals('analogia', $tagsAsArray[1]['name']);
        $this->assertCount(1, $tagsAsArray[1]['aliases']);
        $this->assertEquals('analogy', $tagsAsArray[1]['aliases'][0]);

        $this->assertEquals('api-design', $tagsAsArray[2]['name']);
        $this->assertCount(0, $tagsAsArray[2]['aliases']);
    }

    /**
     * @test
     */
    function it_should_clone_itself()
    {
        $tag1 = (new TagBuilder('tag-1'))
            ->addAlias('tag-name-ALT-1')
            ->build();

        $tag2 = (new TagBuilder('tag-2'))
            ->addAlias('tag-name-ALT-1')
            ->addAlias('tag-name-alt-2')
            ->addAlias('tag-name-alt-3')
            ->build();

        $both = [$tag1, $tag2];

        $tags = Tags::fromArrayOfTag($both);

        $cloned = $tags->clone();

        $this->assertEquals($tags->toArray(), $cloned->toArray());
    }


    /**
     * @test
     */
    function it_should_filter_using_tag_name()
    {
        $tags = Tags::fromYamlFile($this->tagsFile);

        $this->assertCount(1, $tags->filterBy('analog')->getTags());
    }

    /**
     * @test
     */
    function it_should_find_a_tag_by_name()
    {
        $tags = Tags::fromYamlFile($this->tagsFile);

        $this->assertEquals('analogia', $tags->findByName(Name::fromString('analogia'))->getName());
    }


    /**
     * @test
     */
    function it_should_find_a_tag_regardless_of_replaced_characters_name()
    {
        $tag1 = (new TagBuilder('api-design'))
            ->build();

        $tag2 = (new TagBuilder('progettazione-api'))
            ->addAlias('api design')
            ->build();

        $tags = Tags::fromArrayOfTag([$tag1, $tag2]);

        $foundTags = $tags->filterBy('api des');

        $this->assertCount(2, $foundTags->getTags());
    }

    /**
     * @test
     */
    function it_should_not_find_a_tag_when_alias_is_including_chars_that_are_replaced_in_name()
    {
        //This will be returned, because the searched string is always like it was a tag name, thus the chars are replaced
        $tag1 = (new TagBuilder('api-design'))
            ->build();

        //This will not be returned, because the alias needs to be matched without any char replacement
        $tag2 = (new TagBuilder('progettazione-api'))
            ->addAlias('api design')
            ->build();

        $tags = Tags::fromArrayOfTag([$tag1, $tag2]);

        $foundTags = $tags->filterBy('api#des');

        $this->assertCount(1, $foundTags->getTags());
        $this->assertEquals('api-design', $foundTags->getTags()[0]->getName());
    }

    /**
     * @test
     */
    function it_should_not_find_a_tag_by_name_using_an_alias()
    {
        $tags = Tags::fromArrayOfTag([
            (new TagBuilder('analogia'))->addAlias('analogy')->build()
        ]);

        $this->assertNull($tags->findByName(Name::fromString('analogy')));
    }


    /**
     * @test
     */
    function it_should_filter_using_tag_name_regardless_of_case()
    {
        $tags = Tags::fromYamlFile($this->tagsFile);

        $this->assertCount(1, $tags->filterBy('ANALOG')->getTags());
    }

    /**
     * @test
     */
    function it_should_filter_using_alias()
    {
        $tags = Tags::fromYamlFile($this->tagsFile);

        $filteredTags = $tags->filterBy('automation')->getTags();
        $this->assertCount(2, $filteredTags);

        $this->assertEquals('automazione', $filteredTags[0]->getName());
        $this->assertEquals('software-automation', $filteredTags[1]->getName());
    }

    /**
     * @test
     */
    function it_should_add_a_tag()
    {
        $tag1 = (new TagBuilder('tag-1'))
            ->addAlias('tag-name-ALT-1')
            ->build();

        $tag2 = (new TagBuilder('tag-2'))
            ->addAlias('tag-name-ALT-1')
            ->addAlias('tag-name-alt-2')
            ->addAlias('tag-name-alt-3')
            ->build();

        $both = [$tag1, $tag2];

        $tags = Tags::fromArrayOfTag($both);

        $newTag = (new TagBuilder('tag-2'))
            ->addAlias('tag-name-ALT-1')
            ->addAlias('tag-name-alt-2')
            ->addAlias('tag-name-alt-3')
            ->build();

        $tags->add($newTag);

        $this->assertCount(3, $tags->getTags());
    }

}
