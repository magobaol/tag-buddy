<?php

namespace App\TagsSearch;

use App\Exception\TagsSearchOutputFormatNotValid;

class OutputFormat
{
    public const FORMAT_SIMPLE_LIST = 'simple-list';
    public const FORMAT_YAML = 'yaml';
    public const FORMAT_ALFRED = 'alfred';

    private static array $formats = [
        self::FORMAT_SIMPLE_LIST,
        self::FORMAT_YAML,
        self::FORMAT_ALFRED,
    ];

    private string $format;

    private function __construct($format)
    {
        $this->format = $format;
    }

    public static function fromString($string): self
    {
        if (!self::isValid($string)) {
            throw TagsSearchOutputFormatNotValid::createWithDetails(['format' => $string]);
        }

        return new self($string);
    }

    public static function getDefaultFormat(): self
    {
        return self::fromString(static::$formats[0]);
    }

    public static function isValid($format): bool
    {
        return in_array($format, static::$formats);
    }

    public static function Alfred(): self
    {
        return self::fromString(self::FORMAT_ALFRED);
    }

    public static function SimpleList(): self
    {
        return self::fromString(self::FORMAT_SIMPLE_LIST);
    }

    public static function Yaml(): self
    {
        return self::fromString(self::FORMAT_YAML);
    }

    public function isAlfred(): bool
    {
        return $this->format == self::FORMAT_ALFRED;
    }

    public function isSimpleList(): bool
    {
        return $this->format == self::FORMAT_SIMPLE_LIST;
    }

    public function isYaml(): bool
    {
        return $this->format == self::FORMAT_YAML;
    }

}