<?php

namespace App\Exception;

use Common\Exception\TagBuddyException;

class TagsSearchOutputFormatNotValid extends TagBuddyException
{
    protected $code = TagBuddyException::TAG_SEARCH_OUTPUT_FORMAT_NOT_VALID;
    protected const MESSAGE = 'The requested output format is not valid';
}