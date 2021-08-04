<?php

namespace App\Domain\Interfaces;

use App\Domain\Interfaces\UserEntity;
use App\Models\PasswordValueObject;

interface UserRepository
{
    public function exists(UserEntity $user): bool;

    public function create(UserEntity $user, PasswordValueObject $password): UserEntity;
}
