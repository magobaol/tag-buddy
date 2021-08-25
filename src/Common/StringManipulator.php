<?php

namespace Common;

class StringManipulator
{
    private string $value;

    public static function with($string): StringManipulator
    {
        return new self($string);
    }

    private function __construct(string $input) {
        $this->value = $input;
    }

    public function getResult(): string
    {
        return $this->value;
    }

    public function stripHashtag(): self {
        $this->value = str_replace('#', '', $this->value);
        return $this;
    }

    public function replaceSpacesWithDashes(): self
    {
        $this->value = str_replace(' ', '-', $this->value);
        return $this;
    }

    public function replaceHashtagWithDashes(): self
    {
        $this->value = str_replace('#', '-', $this->value);
        return $this;
    }

    public function replaceSlashesWithDashes(): self
    {
        $this->value = str_replace('/', '-', $this->value);
        return $this;
    }

    public function trim(): self
    {
        $this->value = trim($this->value);
        return $this;
    }

    public function stripLeadingNumbers(): self
    {
        $this->value = ltrim($this->value, '0123456789');
        return $this;
    }
}
