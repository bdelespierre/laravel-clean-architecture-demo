<?php

namespace App\Models;

use App\Models\HashedPasswordValueObject;

class PasswordValueObject
{
    public const VALIDATION_REGEX = "/\w{6,}/";

    private string $value;

    public function __construct(string $password)
    {
        if (! filter_var($password, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => self::VALIDATION_REGEX]])) {
            throw new \DomainException("Invalid password.");
        }

        $this->value = $password;
    }

    public function __toString()
    {
        return $this->value;
    }

    public function hashed(): HashedPasswordValueObject
    {
        return HashedPasswordValueObject::hash($this);
    }
}
