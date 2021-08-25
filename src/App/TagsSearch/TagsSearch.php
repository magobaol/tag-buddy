<?php

namespace App\TagsSearch;

use Model\Tag\Tags;

interface TagsSearch
{
    public function __construct(Tags $tags);
    public function search(string $search): string;
}