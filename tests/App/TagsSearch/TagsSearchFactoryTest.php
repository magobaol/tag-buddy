<?php

namespace Tests\App\TagsSearch;

use App\TagsSearch\Alfred;
use App\TagsSearch\OutputFormat;
use App\TagsSearch\SimpleList;
use App\TagsSearch\TagsSearchFactory;
use App\TagsSearch\Yaml;
use Model\Tag\Tags;
use PHPUnit\Framework\TestCase;

class TagsSearchFactoryTest extends TestCase
{
    public function test_make_returns_Alfred()
    {
        $outputFormat = OutputFormat::Alfred();
        $tags = Tags::fromArrayOfTag([]);

        $search = TagsSearchFactory::make($tags, $outputFormat);

        $this->assertInstanceOf(Alfred::class, $search);
    }

    public function test_make_returns_SimpleList()
    {
        $outputFormat = OutputFormat::SimpleList();
        $tags = Tags::fromArrayOfTag([]);

        $search = TagsSearchFactory::make($tags, $outputFormat);

        $this->assertInstanceOf(SimpleList::class, $search);
    }

    public function test_make_returns_Yaml()
    {
        $outputFormat = OutputFormat::Yaml();
        $tags = Tags::fromArrayOfTag([]);

        $search = TagsSearchFactory::make($tags, $outputFormat);

        $this->assertInstanceOf(Yaml::class, $search);
    }

}