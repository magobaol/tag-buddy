<?php

namespace App\Command;

use App\Model\Tag\Tags;
use Symfony\Component\Yaml\Yaml;

class TagSearch
{
    /**
     * @var array|string[]
     */
    private static array $formats = [
        'list',
        'yaml',
    ];

    /**
     * @var Tags
     */
    private Tags $tags;

    public function __construct()
    {
        $this->tags = Tags::fromYamlFile('/Users/francesco/dev/tag-buddy/tags.yaml');
    }

    public function getResult($search, $outputFormat): string
    {
        $filteredTags = $this->tags->filterBy($search);
        switch ($outputFormat) {
            case 'list':
                return $filteredTags->toStringAsListOfNames();
            case 'yaml':
                return Yaml::dump($filteredTags->toArrayWithNamesAsKeys());

        }
    }

    public static function getOutputFormats(): array
    {
        return self::$formats;
    }

    public static function getDefaultOutputFormat(): string
    {
        return self::$formats[0];
    }
}
