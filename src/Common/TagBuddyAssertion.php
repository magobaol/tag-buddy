<?php

namespace Common;

use Assert\Assertion as BaseAssertion;

class TagBuddyAssertion extends BaseAssertion
{
    protected static $exceptionClass = 'Common\Exception\AssertionFailed';
}
