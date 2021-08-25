<?php

namespace Tests\Model\Tag\Builder;

use Model\Tag\Tag;

class TagBuilder
{
    private string $name;
    private array $aliases = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function withName($name): self
    {
        $this->name = $name;
        return $this;
    }

    public function addAlias($alias): self
    {
        $this->aliases[] = $alias;
        return $this;
    }

    public function build(): Tag
    {
        return Tag::fromArray([
            'name' => $this->name,
            'aliases' => $this->aliases,
        ]);

    }
}