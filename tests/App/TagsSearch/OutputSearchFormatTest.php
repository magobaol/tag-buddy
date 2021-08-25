<?php

namespace Tests\App\TagsSearch;

use App\Exception\TagsSearchOutputFormatNotValid;
use App\TagsSearch\OutputFormat;
use PHPUnit\Framework\TestCase;

class OutputSearchFormatTest extends TestCase
{
    public function test_fromString_withInvalidFormat_throwsException()
    {
        $this->expectException(TagsSearchOutputFormatNotValid::class);

        $format = OutputFormat::fromString('not-valid');
    }

    public function test_isValid_withValidFormat_returns_true()
    {
        $this->assertTrue(OutputFormat::isValid('alfred'));
    }

    public function test_isValid_withNotValidFormat_returns_false()
    {
        $this->assertFalse(OutputFormat::isValid('not-valid'));
    }

    public function test_isAlfred_returns_true()
    {
        $format = OutputFormat::fromString(OutputFormat::FORMAT_ALFRED);

        $this->assertTrue($format->isAlfred());
    }

    public function test_isAlfred_returns_false()
    {
        $format = OutputFormat::fromString(OutputFormat::FORMAT_SIMPLE_LIST);

        $this->assertFalse($format->isAlfred());
    }

    public function test_isSimpleList_returns_true()
    {
        $format = OutputFormat::fromString(OutputFormat::FORMAT_SIMPLE_LIST);

        $this->assertTrue($format->isSimpleList());
    }

    public function test_isSimpleList_returns_false()
    {
        $format = OutputFormat::fromString(OutputFormat::FORMAT_ALFRED);

        $this->assertFalse($format->isSimpleList());
    }

    public function test_isYaml_returns_true()
    {
        $format = OutputFormat::fromString(OutputFormat::FORMAT_YAML);

        $this->assertTrue($format->isYaml());
    }

    public function test_isYaml_returns_false()
    {
        $format = OutputFormat::fromString(OutputFormat::FORMAT_ALFRED);

        $this->assertFalse($format->isYaml());
    }
}