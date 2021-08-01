<?php

namespace App\Tests\Model\Tag;

use App\Model\Tag\Aliases;
use PHPUnit\Framework\TestCase;

class AliasesTest extends TestCase
{

    /**
     * @test
     */
    public function it_should_create_collection_from_array_of_strings()
    {
        $strings = [
            'this is an alias',
            'this is another alias'
        ];

        $aliases = Aliases::fromArrayOfStrings($strings);

        $this->assertCount(2, $aliases->toArray());
    }

    /**
     * @test
     */
    public function it_should_create_an_empty_collection_from_empty_array_of_strings()
    {
        $strings = [];

        $aliases = Aliases::fromArrayOfStrings($strings);

        $this->assertCount(0, $aliases->toArray());
    }
}
