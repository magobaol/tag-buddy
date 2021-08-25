<?php

namespace Tests\Common;

use Common\StringManipulator;
use PHPUnit\Framework\TestCase;

class StringManipulatorTest extends TestCase
{
    /**
     * @test
     */
    function it_should_returns_the_input_unchanged() {

        $input = '8 #. very weird 9 string';
        $sm = StringManipulator::with($input);

        $this->assertEquals($input, $sm->getResult());
    }

    /**
     * @test
     */
    function it_should_strip_hashtag() {

        $input = '#str#ing#';
        $sm = StringManipulator::with($input);

        $this->assertEquals('string', $sm->stripHashtag()->getResult());
    }

    /**
     * @test
     */
    function it_should_replace_spaces_with_dashes() {

        $input = 'string with spaces';
        $sm = StringManipulator::with($input);

        $this->assertEquals('string-with-spaces', $sm->replaceSpacesWithDashes()->getResult());
    }

    /**
     * @test
     */
    function it_should_replace_hashtag_with_dashes() {

        $input = 'string#with#hashtag';
        $sm = StringManipulator::with($input);

        $this->assertEquals('string-with-hashtag', $sm->replaceHashtagWithDashes()->getResult());
    }

    /**
     * @test
     */
    function it_should_replace_slashes_with_dashes() {

        $input = 'string/with/slashes';
        $sm = StringManipulator::with($input);

        $this->assertEquals('string-with-slashes', $sm->replaceSlashesWithDashes()->getResult());
    }

    /**
     * @test
     * @dataProvider it_should_trim_surrounding_spaces_data
     */
    function it_should_trim_surrounding_spaces($input, $result) {

        $sm = StringManipulator::with($input);

        $this->assertEquals($result, $sm->trim()->getResult());
    }

    public function it_should_trim_surrounding_spaces_data(): array
    {
        return [
            [
                ' string with surrounding spaces ',
                'string with surrounding spaces'
            ],
            [
                '    string with surrounding spaces    ',
                'string with surrounding spaces'
            ],
            [
                PHP_EOL.'string with surrounding EOL'.PHP_EOL,
                'string with surrounding EOL'
            ],
            [
                PHP_EOL.' string with surrounding spaces and EOL  '.PHP_EOL,
                'string with surrounding spaces and EOL'
            ],
        ];
    }

    /**
     * @test
     * @dataProvider stripLeadingNumbers_data
     */
    function it_should_strip_leading_numbers($input, $result) {
        $this->assertEquals($result, StringManipulator::with($input)->stripLeadingNumbers()->getResult());
    }

    public function stripLeadingNumbers_data(): array
    {
        return [
            [
                '0123456789string',
                'string',
            ],
            [
                '0123456789string0123456789',
                'string0123456789',
            ],
        ];
    }

    /**
     * @test
     */
    public function it_should_replace_trailing_spaces_with_dashes() {

        $input = ' string ';
        $result = StringManipulator::with($input)->replaceSpacesWithDashes()->trim()->getResult();

        $this->assertEquals('-string-', $result);
    }

    /**
     * @test
     */
    public function it_should_trim_trailing_spaces_and_have_no_dashes() {

        $input = ' string ';
        $result = StringManipulator::with($input)->trim()->replaceSpacesWithDashes()->getResult();

        $this->assertEquals('string', $result);
    }

}
