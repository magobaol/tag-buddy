<?php

namespace App\Tests\Common\Exception;

use App\Common\Exception\TagBuddyException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class TagBuddyExceptionTest extends TestCase
{
    public function test_ErrorCodesAreUnique()
    {
        $TagBuddyExceptionClass = new ReflectionClass('App\Common\Exception\TagBuddyException');
        $errorsDefined = $TagBuddyExceptionClass->getConstants();
        $uniqueErrorCodes = array_unique($errorsDefined);
        $notUniqueErrorCodes = implode(', ', array_diff_assoc($errorsDefined, $uniqueErrorCodes));

        $this->assertEquals(count($errorsDefined), count($uniqueErrorCodes), 'Not unique codes are: '.$notUniqueErrorCodes);
    }
}

