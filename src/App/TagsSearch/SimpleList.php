<?php

namespace App\TagsSearch;

use Model\Tag\Tags;

class SimpleList implements TagsSearch
{
    private Tags $tags;

    public function __construct(Tags $tags)
    {
        $this->tags = $tags;
    }

    public function search(string $search): string
    {
        $filteredTags = $this->tags->filterBy($search);
        return $filteredTags->toStringAsListOfNames();
    }
}