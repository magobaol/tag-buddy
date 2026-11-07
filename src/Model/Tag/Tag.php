<?php

namespace Model\Tag;

use Common\Exception\AliasNotFound;
use Common\Exception\TagNameAlreadyUsed;
use Common\TagBuddyAssertion;

class Tag
{
    private Name $name;
    private Aliases $aliases;
    private ?string $description;

    public static function fromArray($array): Tag
    {
        TagBuddyAssertion::keyExists($array, 'name');

        return new Tag(
            Name::fromString($array['name']),
            array_key_exists('aliases', $array) ?
                Aliases::fromArrayOfStrings($array['aliases']) :
                Aliases::fromArrayOfStrings([]),
            isset($array['description']) ? (string) $array['description'] : null
        );
    }

    public static function fromString($tagName): Tag
    {
        return new Tag(
            Name::fromString($tagName),
            Aliases::fromArrayOfStrings([])
        );
    }

    private function __construct(Name $name, Aliases $aliases, ?string $description = null){
        $this->name = $name;
        $this->aliases = $aliases;
        $description = $description === null ? null : trim($description);
        $this->description = $description === '' ? null : $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * An empty description clears it.
     */
    public function withDescription(?string $description): Tag
    {
        return new self($this->name, $this->aliases, $description);
    }

    /**
     * @throws TagNameAlreadyUsed when the tag already has the alias
     */
    public function withAddedAlias(string $alias): Tag
    {
        $alias = Alias::fromString($alias);
        if ($this->aliases->contains($alias)) {
            throw TagNameAlreadyUsed::forValue($alias->toString(), $this->getName());
        }

        return new self($this->name, $this->aliases->with($alias), $this->description);
    }

    /**
     * @throws AliasNotFound when the tag does not have the alias
     */
    public function withoutAlias(string $alias): Tag
    {
        $alias = Alias::fromString($alias);
        if (!$this->aliases->contains($alias)) {
            throw AliasNotFound::forAlias($alias->toString(), $this->getName());
        }

        return new self($this->name, $this->aliases->without($alias), $this->description);
    }

    /**
     * @return string[] the name followed by the aliases
     */
    public function getNameAndAliases(): array
    {
        return array_merge([$this->getName()], $this->getAliasesAsArray());
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
        $array = [
            'name' => $this->name->toString(),
            'aliases' => $this->aliases->toArray()
        ];
        if ($this->description !== null) {
            $array['description'] = $this->description;
        }

        return $array;
    }

    public function clone(): Tag
    {
        return new self(
            $this->name,
            $this->aliases,
            $this->description
        );
    }
}
