<?php

namespace App\TagsSearch;

use Model\Tag\Name;
use Model\Tag\Tags;

class Alfred implements TagsSearch
{
    private Tags $tags;

    public function __construct(Tags $tags)
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

        $searchAsTagName = Name::fromString($search);
        if (($search != '') && ($this->tags->findByName($searchAsTagName) == null)) {
            $items[] = [
                'title' => $searchAsTagName->toString(),
                'subtitle' => sprintf("Add %s as new tag",$searchAsTagName->toString()),
                'arg' => sprintf("add::%s", $searchAsTagName->toString())
            ];
        }

        //Let's make sure that the final array is correctly indexed, otherwise the resulting json could be wrong
        $items = array_values($items);

        return json_encode(['items' => $items]);
    }
}