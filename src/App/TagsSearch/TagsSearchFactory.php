<?php

namespace App\TagsSearch;

use Model\Tag\Tags;

class TagsSearchFactory
{
    public static function make(string $inputFile, OutputFormat $outputFormat): TagsSearch
    {
        $tags = Tags::fromYamlFile($inputFile);
        if ($outputFormat->isAlfred()) {
            return new Alfred($tags);
        }

        if ($outputFormat->isSimpleList()) {
            return new SimpleList($tags);
        }

        if ($outputFormat->isYaml()) {
            return new Yaml($tags);
        }
    }
}