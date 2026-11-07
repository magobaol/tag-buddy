<?php

namespace Common\Exception;

class TagNameAlreadyUsed extends TagBuddyException
{
    protected $code = TagBuddyException::TAG_NAME_ALREADY_USED;

    public static function forValue(string $value, string $usedBy): self
    {
        $e = new self(sprintf('"%s" is already used by tag "%s"', $value, $usedBy));
        $e->setDetails(['value' => $value, 'usedBy' => $usedBy]);

        return $e;
    }
}
