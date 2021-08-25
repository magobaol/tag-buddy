<?php

namespace Model\Tag;

use Common\TagBuddyAssertion;

class Tag
{
    private Name $name;
    private Aliases $aliases;

    public static function fromArray($array): Tag
    {
        TagBuddyAssertion::keyExists($array, 'name');

        return new Tag(
            Name::fromString($array['name']),
            array_key_exists('aliases', $array) ?
                Aliases::fromArrayOfStrings($array['aliases']) :
                Aliases::fromArrayOfStrings([])
        );
    }

    public static function fromString($tagName): Tag
    {
        return new Tag(
            Name::fromString($tagName),
            Aliases::fromArrayOfStrings([])
        );
    }

    private function __construct(Name $name, Aliases $aliases){
        $this->name = $name;
        $this->aliases = $aliases;
    }

    public function getName(): string
    {
        return $this->name->toString();
    }

    public function getAliasesAsArray(): array
    {
        return $this->aliases->toArray();
    }

    public function getAliasesAsOneString(): string
    {
        return implode("; ", $this->aliases->toArray());
    }

    public function getSearchableLoweredString(): string
    {
        return strtolower($this->name->toString()).'; '.strtolower($this->getAliasesAsOneString());
    }

    public function getSearchableString(): string
    {
        return $this->name->toString().'; '.$this->getAliasesAsOneString();
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name->toString(),
            'aliases' => $this->aliases->toArray()
        ];
    }

    public function clone(): Tag
    {
        return new self(
            $this->name,
            $this->aliases
        );
    }
}
