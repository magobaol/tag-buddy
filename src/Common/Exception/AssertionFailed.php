<?php

namespace App\Common\Exception;

class AssertionFailed extends TagBuddyException
{
    protected $code = TagBuddyException::ASSERTION_FAILED;
    protected const MESSAGE = 'Assertion failed: %s. See details.';

    public function __construct($message, $code, $propertyPath, $value, array $constraints = [])
    {
        parent::__construct(sprintf(self::MESSAGE, $message));
        $this->setDetails([
            'message' => $message,
            'propertyPath' => $propertyPath,
            'value' => $value,
            'constraints' => $constraints,
            'code' => $code,
        ]);
    }
}