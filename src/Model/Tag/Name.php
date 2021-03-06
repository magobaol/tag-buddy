<?php

namespace Model\Tag;

use Common\StringManipulator;
use Common\TagBuddyAssertion;

class Name
{
    /**
     * @var string
     */
    private string $value;

    private static function cleanSurroundings($tag): string
    {
        $result = StringManipulator::with($tag)
            ->trim()
            ->stripLeadingNumbers()
            ->getResult();
        if ($result != $tag) {
            return self::cleanSurroundings($result);
        } else {
            return $result;
        }
    }

    public static function fromString($tag): Name
    {
        TagBuddyAssertion::string($tag);
        $tag = self::cleanSurroundings($tag);
        return new Name(
            StringManipulator::with($tag)
                ->replaceSpacesWithDashes()
                ->replaceHashtagWithDashes()
                ->replaceSlashesWithDashes()
                ->getResult()
        );
    }

    private function __construct(
        string $value
    ) {
        $this->value = $value;
    }

    public function toString()
    {
        return $this->value;
    }
}
