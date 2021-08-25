<?php

namespace Tests\Model\Tag;

use Common\Exception\AssertionFailed;
use Model\Tag\Name;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    /**
     * @test
     */
    function it_should_replace_spaces_with_dashes()
    {
        $tagName = Name::fromString('this is a tag');
        $this->assertEquals('this-is-a-tag', $tagName->toString());
    }

    /**
     * @test
     */
    function it_should_not_create_with_array()
    {
        $this->expectException(AssertionFailed::class);

        Name::fromString([]);
    }

    /**
     * @test
     */
    function it_should_not_create_with_number()
    {
        $this->expectException(AssertionFailed::class);

        Name::fromString(9);
    }

    /**
     * @test
     */
    function it_should_replace_hashtags()
    {
        $tagName = Name::fromString('#t#ag#');
        $this->assertEquals('-t-ag-', $tagName->toString());
    }

    /**
     * @test
     */
    function it_should_trim_surrounding_spaces()
    {
        $tagName = Name::fromString(' tag ');
        $this->assertEquals('tag', $tagName->toString());
    }

    /**
     * @test
     */
    function it_should_trim_linefeed()
    {
        $tagName = Name::fromString(PHP_EOL.'tag'.PHP_EOL);
        $this->assertEquals('tag', $tagName->toString());
    }

    /**
     * @test
     * @dataProvider it_should_get_a_clean_name_data
     */
    function it_should_get_a_clean_name($input, $result) {
        $this->assertEquals($result, Name::fromString($input)->toString());
    }

    public function it_should_get_a_clean_name_data()
    {
        return [
            [
                '1 tag',
                'tag'
            ],
            [
                ' 2tag',
                'tag'
            ],
            [
                ' 3 tag',
                'tag'
            ],
            [
                ' 4 5 tag',
                'tag'
            ],
            [
                ' 6 7 tag word',
                'tag-word'
            ],
            [
                ' 8 9 tag word 10',
                'tag-word-10'
            ],
            [
                ' 8 9 tag#word#10',
                'tag-word-10'
            ],
            [
                ' 8 9 tag#word/10',
                'tag-word-10'
            ],
        ];
    }

}
