<?php

namespace Common\Exception;

class AliasNotFound extends TagBuddyException
{
    protected $code = TagBuddyException::ALIAS_NOT_FOUND;

    public static function forAlias(string $alias, string $tagName): self
    {
        $e = new self(sprintf('Tag "%s" has no alias "%s"', $tagName, $alias));
        $e->setDetails(['alias' => $alias, 'tag' => $tagName]);

        return $e;
    }
}
