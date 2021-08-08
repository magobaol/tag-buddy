<?php

namespace App\Tests\Model\Tag;

use App\Model\Tag\Tag;
use App\Model\Tag\Tags;
use PHPUnit\Framework\TestCase;

class TagsTest extends TestCase
{

    /**
     * @test
     */
    function it_should_load_an_empty_list_if_yaml_does_not_exists()
    {
        $tags = Tags::fromYamlFile(dirname(__FILE__).'/not-existing-tags.yaml');

        $tagsAsArray = $tags->toArray();
        $this->assertCount(0, $tagsAsArray);
    }

    /**
     * @test
     */
    function it_should_load_tags_from_yaml_file()
    {
        $tags = Tags::fromYamlFile(dirname(__FILE__).'/tags.yaml');

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
        $tag1 = Tag::fromArray([
            'name' => 'tag-1',
            'aliases' => [
                'tag-name-ALT-1',
            ]
        ]);

        $tag2 = Tag::fromArray([
            'name' => 'tag-2',
            'aliases' => [
                'tag-name-ALT-1',
                'tag-name-alt-2',
                'tag-name-alt-3'
            ]
        ]);

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
        $tags = Tags::fromYamlFile(dirname(__FILE__).'/tags.yaml');

        $this->assertCount(1, $tags->filterBy('analog')->getTags());
    }

    /**
     * @test
     */
    function it_should_find_a_tag_by_name()
    {
        $tags = Tags::fromYamlFile(dirname(__FILE__).'/tags.yaml');

        $this->assertEquals('analogia', $tags->findByName('analogia')->getName());
    }


    /**
     * @test
     */
    function it_should_not_find_a_tag_by_name_using_an_alias()
    {
        $tags = Tags::fromYamlFile(dirname(__FILE__).'/tags.yaml');

        $this->assertNull($tags->findByName('analogy'));
    }


    /**
     * @test
     */
    function it_should_filter_using_tag_name_regardless_of_case()
    {
        $tags = Tags::fromYamlFile(dirname(__FILE__).'/tags.yaml');

        $this->assertCount(1, $tags->filterBy('ANALOG')->getTags());
    }

    /**
     * @test
     */
    function it_should_filter_using_alias()
    {
        $tags = Tags::fromYamlFile(dirname(__FILE__).'/tags.yaml');

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
        $tag1 = Tag::fromArray([
            'name' => 'tag-1',
            'aliases' => [
                'tag-name-ALT-1',
            ]
        ]);

        $tag2 = Tag::fromArray([
            'name' => 'tag-2',
            'aliases' => [
                'tag-name-ALT-1',
                'tag-name-alt-2',
                'tag-name-alt-3'
            ]
        ]);

        $both = [$tag1, $tag2];

        $tags = Tags::fromArrayOfTag($both);

        $newTag = Tag::fromArray([
            'name' => 'tag-2',
            'aliases' => [
                'tag-name-ALT-1',
                'tag-name-alt-2',
                'tag-name-alt-3'
            ]
        ]);

        $tags->add($newTag);

        $this->assertCount(3, $tags->getTags());
    }

}
