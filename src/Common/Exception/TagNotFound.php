<?php

namespace Common\Exception;

class TagNotFound extends TagBuddyException
{
    protected $code = TagBuddyException::TAG_NOT_FOUND;

    public static function forName(string $name): self
    {
        $e = new self(sprintf('Tag "%s" not found', $name));
        $e->setDetails(['name' => $name]);

        return $e;
    }
}
