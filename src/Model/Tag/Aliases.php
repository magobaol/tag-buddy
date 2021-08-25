<?php

namespace Model\Tag;

class Aliases
{
    /**
     * @var Alias[]
     */
    private array $aliases;

    private function __construct()
    {
        $this->aliases = [];
    }

    public static function fromArrayOfStrings($aliases): self
    {
        $collection = new self();
        foreach ($aliases as $alias) {
            $collection->add(Alias::fromString($alias));
        }

        return $collection;
    }

    public function add(Alias $alias): void
    {
        $this->aliases[] = $alias;
    }

    public function toArray(): array
    {
        return array_map(function (Alias $alias) { return $alias->toString(); }, $this->aliases);
    }


}
