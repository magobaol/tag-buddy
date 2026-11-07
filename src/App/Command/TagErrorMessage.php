<?php

namespace App\Command;

use Common\Exception\AliasNotFound;
use Common\Exception\TagBuddyException;
use Common\Exception\TagNameAlreadyUsed;
use Common\Exception\TagNotFound;

/**
 * The text the user reads when a tag operation is refused.
 */
class TagErrorMessage
{
    public static function for(TagBuddyException $e): string
    {
        $details = $e->getDetails();

        return match (true) {
            $e instanceof TagNameAlreadyUsed => sprintf('«%s» è già usato dal tag %s', $details['value'], $details['usedBy']),
            $e instanceof TagNotFound => sprintf('Tag «%s» non trovato', $details['name']),
            $e instanceof AliasNotFound => sprintf('Alias «%s» non presente nel tag %s', $details['alias'], $details['tag']),
            default => 'Operazione non riuscita',
        };
    }
}
