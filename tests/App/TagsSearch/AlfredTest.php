<?php

namespace Tests\App\TagsSearch;

use App\TagsSearch\Alfred;
use Model\Tag\Tag;
use Model\Tag\Tags;
use PHPUnit\Framework\TestCase;
use Tests\Model\Tag\Builder\TagBuilder;

class AlfredTest extends TestCase
{
    public function test_search_it_should_return_the_right_json()
    {
        $tag1 = (new TagBuilder('poesia'))
            ->addAlias('poetry')
            ->addAlias('poésie')
            ->build();

        $tag2 = (new TagBuilder('sviluppo'))
            ->addAlias('development')
            ->addAlias('coding')
            ->build();

        $tags = Tags::fromArrayOfTag([$tag1, $tag2]);

        $search = new Alfred($tags);

        $result = $search->search('');

        $json1 = '{"title":"poesia","subtitle":"poetry; po\u00e9sie","arg":"use::poesia","match":"poesia; poetry; po\u00e9sie","autocomplete":"poesia"}';
        $json2 = '{"title":"sviluppo","subtitle":"development; coding","arg":"use::sviluppo","match":"sviluppo; development; coding","autocomplete":"sviluppo"}';
        $expectedJson = sprintf('{"items":[%s,%s]}', $json1, $json2);
        
        $this->assertEquals($expectedJson, $result);
    }

    public function test_search_with_no_exact_match_it_should_return_the_add_item_as_last_item()
    {
        $tag1 = (new TagBuilder('poesia-francese'))
            ->addAlias('french-poetry')
            ->build();

        $tag2 = (new TagBuilder('sviluppo'))
            ->addAlias('development')
            ->addAlias('coding')
            ->build();

        $tag3 = (new TagBuilder('poesia-italiana'))
            ->addAlias('italian-poetry')
            ->build();

        $tags = Tags::fromArrayOfTag([$tag1, $tag2, $tag3]);

        $search = new Alfred($tags);

        $result = $search->search('poesia');

        $decoded_result = json_decode($result, true);

        $this->assertCount(3, $decoded_result['items']);

        $this->assertEquals('poesia-francese', $decoded_result['items'][0]['title']);
        $this->assertEquals('use::poesia-francese', $decoded_result['items'][0]['arg']);

        $this->assertEquals('poesia-italiana', $decoded_result['items'][1]['title']);
        $this->assertEquals('use::poesia-italiana', $decoded_result['items'][1]['arg']);

        $this->assertEquals('poesia', $decoded_result['items'][2]['title']);
        $this->assertEquals('add::poesia', $decoded_result['items'][2]['arg']);
    }
}