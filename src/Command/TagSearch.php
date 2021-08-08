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
        'alfred'
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
        switch ($outputFormat) {
            case 'list':
                return $this->getResultsForList($search);
            case 'yaml':
                return $this->getResultsForYaml($search);
            case 'alfred':
                return $this->getResultsForAlfred($search);

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

    private function getResultsForAlfred(string $search): string
    {
        $filteredTags = $this->tags->filterBy($search);
        foreach ($filteredTags->getTags() as $tag) {

            $item = [
                'title' => $tag->getName(),
                'subtitle' => $tag->getAliasesAsOneString(),
                'arg' => 'use::'.$tag->getName(),
                'match' => $tag->getSearchableLoweredString(),
                'autocomplete' => $tag->getName(),
            ];

            $items[] = $item;
        }

        if (($search != '') && ($this->tags->findByName($search) == null)) {
            $items[] = [
                'title' => $search,
                'subtitle' => "Add $search as new tag",
                'arg' => "add::$search",
            ];
        }

        //Let's make sure that the final array is correctly indexed, otherwise the resulting json could be wrong
        $items = array_values($items);

        return json_encode(['items' => $items]);
    }

    protected function getResultsForList(string $search): string
    {
        $filteredTags = $this->tags->filterBy($search);
        return $filteredTags->toStringAsListOfNames();
    }

    private function getResultsForYaml($search)
    {
        $filteredTags = $this->tags->filterBy($search);
        return Yaml::dump($filteredTags->toArrayWithNamesAsKeys());
    }
}
