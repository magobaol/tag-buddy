<?php

namespace App\TagsSearch;

use Model\Tag\Tags;

class TagsSearchFactory
{
    public static function make(Tags $unfilteredTags, OutputFormat $outputFormat): TagsSearch
    {
        if ($outputFormat->isAlfred()) {
            return new Alfred($unfilteredTags);
        }

        if ($outputFormat->isSimpleList()) {
            return new SimpleList($unfilteredTags);
        }

        if ($outputFormat->isYaml()) {
            return new Yaml($unfilteredTags);
        }
    }
}