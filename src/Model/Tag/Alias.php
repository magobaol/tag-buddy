<?php

namespace App\Model\Tag;

use App\Common\StringManipulator;

class Alias
{
    private string $value;

    public static function fromString(string $alias): Alias
    {
        return new self(
            StringManipulator::with($alias)
                ->replaceHashtagWithDashes()
                ->getResult()
        );
    }

    private function __construct($alias) {
        $this->value = $alias;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }
}
