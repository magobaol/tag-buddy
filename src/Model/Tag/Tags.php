<?php

namespace Model\Tag;

use Common\Exception\TagNameAlreadyUsed;
use Common\Exception\TagNotFound;
use Symfony\Component\Yaml\Yaml;

class Tags
{
    /**
     * @var Tag[]
     */
    private array $tags;

    private function __construct()
    {
        $this->tags = [];
    }

    public static function fromYamlFile($filename): Tags
    {
        $tags = new self();
        if (file_exists($filename)) {
            $yaml = Yaml::parseFile($filename);
            foreach ($yaml['tags'] as $yamlTag) {
                $tag = Tag::fromArray($yamlTag);
                $tags->append($tag);
            }
        }
        return $tags;
    }

    public function toYamlFile($filename)
    {
        $yamlData = [
            'tags' => $this->toArrayWithNamesAsKeys()
        ];

        $yamlContent = Yaml::dump($yamlData, 4, 2);

        file_put_contents($filename, $yamlContent);
    }

    public static function fromArrayOfTag($tags): Tags
    {
        $collection = new self();
        foreach ($tags as $tag) {
            $collection->append($tag);
        }

        return $collection;
    }

    /**
     * @throws TagNameAlreadyUsed when the name or an alias is already used by another tag
     */
    public function add(Tag $tag): void
    {
        $this->assertUnique($tag, null);
        $this->append($tag);
    }

    /**
     * Replaces the tag with the same name, keeping its position.
     *
     * @throws TagNotFound when no tag has that name
     * @throws TagNameAlreadyUsed when an alias is already used by another tag
     */
    public function replace(Tag $updated): void
    {
        foreach ($this->tags as $index => $tag) {
            if (strcasecmp($tag->getName(), $updated->getName()) === 0) {
                $this->assertUnique($updated, $tag);
                $this->tags[$index] = $updated;
                return;
            }
        }

        throw TagNotFound::forName($updated->getName());
    }

    /**
     * Loading and filtering do not validate: a file that already has collisions must still be readable.
     */
    private function append(Tag $tag): void
    {
        $this->tags[] = $tag;
    }

    private function assertUnique(Tag $tag, ?Tag $ignoring): void
    {
        $usedBy = [];
        foreach ($this->tags as $existing) {
            if ($existing === $ignoring) {
                continue;
            }
            foreach ($existing->getNameAndAliases() as $value) {
                $usedBy[strtolower($value)] = $existing->getName();
            }
        }

        foreach ($tag->getNameAndAliases() as $value) {
            if (isset($usedBy[strtolower($value)])) {
                throw TagNameAlreadyUsed::forValue($value, $usedBy[strtolower($value)]);
            }
        }
    }

    /**
     * @return Tag[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    public function toArray(): array
    {
        return array_map(function (Tag $tag) { return $tag->toArray(); }, $this->tags);
    }

    public function toArrayWithNamesAsKeys(): array
    {
        $result = [];
        foreach ($this->tags as $tag) {
            $result[$tag->getName()] = $tag->toArray();
        }
        return $result;
    }

    public function toStringAsListOfNames()
    {
        $list = array_map(function (Tag $tag) { return $tag->getName(); }, $this->tags);
        return implode(PHP_EOL, $list);
    }

    /**
     * @param string $search
     * @return Tags
     */
    public function filterBy(string $search): Tags
    {
        if ($search == '') {
            return $this->clone();
        }
        $results = [];

        $searchAsTagName = Name::fromString($search);
        foreach ($this->tags as $tag) {
            if (
                ($this->searchStringIsInAliases($tag, $search)) ||
                ($this->searchStringIsInName($tag, $searchAsTagName))
                ) {
                $results[] = $tag;
            }
        }

        return self::fromArrayOfTag($results);
    }

    public function findByName(Name $name): ?Tag
    {
        foreach ($this->tags as $tag) {
            if (strtolower($tag->getName()) == strtolower($name->toString())) {
                return $tag;
            }
        }

        return null;
    }

    public function clone(): self
    {
        $cloned = new self();

        foreach ($this->tags as $tag) {
            $cloned->append($tag->clone());
        }

        return $cloned;
    }

    public function searchStringIsInAliases(Tag $tag, string $search): bool
    {
        return stripos($tag->getAliasesAsOneString(), $search) !== false;
    }

    public function searchStringIsInName(Tag $tag, Name $searchAsTagName): bool
    {
        return stripos($tag->getName(), $searchAsTagName->toString()) !== false;
    }
}
