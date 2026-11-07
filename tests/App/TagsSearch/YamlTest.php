<?php

namespace Tests\App\TagsSearch;

use App\TagsSearch\Alfred;
use App\TagsSearch\SimpleList;
use App\TagsSearch\Yaml;
use Model\Tag\Tag;
use Model\Tag\Tags;
use PHPUnit\Framework\TestCase;

class YamlTest extends TestCase
{
    private function tags(): Tags
    {
        return Tags::fromArrayOfTag([
            Tag::fromArray(['name' => 'ai-coding', 'aliases' => ['coding'], 'description' => 'Writing code with an assistant']),
        ]);
    }

    /**
     * @test
     */
    function it_should_show_the_description_in_the_yaml_output()
    {
        $output = (new Yaml($this->tags()))->search('');

        $this->assertStringContainsString('description:', $output);
        $this->assertStringContainsString('Writing code with an assistant', $output);
    }

    /**
     * @test
     */
    function it_should_not_show_the_description_in_the_alfred_output()
    {
        $output = (new Alfred($this->tags()))->search('');

        $this->assertStringNotContainsString('Writing code with an assistant', $output);
    }

    /**
     * @test
     */
    function it_should_not_show_the_description_in_the_simple_list()
    {
        $output = (new SimpleList($this->tags()))->search('');

        $this->assertStringNotContainsString('Writing code with an assistant', $output);
    }
}
