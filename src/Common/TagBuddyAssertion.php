<?php

namespace App\Common;

use Assert\Assertion as BaseAssertion;

class TagBuddyAssertion extends BaseAssertion
{
    protected static $exceptionClass = 'App\Common\Exception\AssertionFailed';
}
