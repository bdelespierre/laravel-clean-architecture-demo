<?php

namespace App\Models;

class EmailValueObject
{
    private string $value;

    public function __construct(string $value)
    {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \DomainException("Invalid email '{$value}'.");
        }

        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }

    public function isEqualTo(self $email): bool
    {
        return $this->value == $email->value;
    }
}
