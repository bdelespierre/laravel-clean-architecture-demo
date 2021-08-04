<?php

namespace App\Repositories;

use App\Domain\Interfaces\UserEntity;
use App\Domain\Interfaces\UserRepository;
use App\Models\PasswordValueObject;
use App\Models\User;

class UserDatabaseRepository implements UserRepository
{
    public function exists(UserEntity $user): bool
    {
        return User::where([
            'name' => $user->getName(),
            'email' => (string) $user->getEmail(),
        ])->exists();
    }

    public function create(UserEntity $user, PasswordValueObject $password): UserEntity
    {
        return User::create([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $password,
        ]);
    }
}
