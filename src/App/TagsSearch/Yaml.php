<?php

namespace App\TagsSearch;

use Model\Tag\Tags;

class Yaml implements TagsSearch
{
    private Tags $tags;

    public function __construct($tags)
    {
        $this->tags = $tags;
    }

    public function search(string $search): string
    {
        $filteredTags = $this->tags->filterBy($search);
        return \Symfony\Component\Yaml\Yaml::dump($filteredTags->toArrayWithNamesAsKeys());
    }
}