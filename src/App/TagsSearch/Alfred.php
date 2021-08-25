<?php

namespace App\TagsSearch;

use Model\Tag\Tags;

class Alfred implements TagsSearch
{
    private Tags $tags;

    public function __construct($tags)
    {
        $this->tags = $tags;
    }

    public function search(string $search): string
    {
        $items = [];
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
}