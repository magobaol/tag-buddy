<?php

namespace Tests\Model\Tag;

use Model\Tag\Alias;
use PHPUnit\Framework\TestCase;

class AliasTest extends TestCase
{
    /**
     * @test
     */
    function it_should_get_any_character() {
        $value = 'tag with a lot of weird characters ;-_@\|';

        $alias = Alias::fromString($value);

        $this->assertEquals($value, $alias->toString());
    }

    /**
     * @test
     */
    function it_should_reaplce_hashtags_with_dashes()
    {
        $tagName = Alias::fromString('#a#lias#');
        $this->assertEquals('-a-lias-', $tagName->toString());
    }

}
