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

    public function contains(Alias $alias): bool
    {
        foreach ($this->aliases as $existing) {
            if (strcasecmp($existing->toString(), $alias->toString()) === 0) {
                return true;
            }
        }

        return false;
    }

    public function with(Alias $alias): self
    {
        return self::fromArrayOfStrings(array_merge($this->toArray(), [$alias->toString()]));
    }

    public function without(Alias $alias): self
    {
        return self::fromArrayOfStrings(array_values(array_filter(
            $this->toArray(),
            fn (string $existing) => strcasecmp($existing, $alias->toString()) !== 0
        )));
    }

}
