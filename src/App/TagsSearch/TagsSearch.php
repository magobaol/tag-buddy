<?php

namespace App\TagsSearch;

interface TagsSearch
{
    public function search(string $search): string;
}