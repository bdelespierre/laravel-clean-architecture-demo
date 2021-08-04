<?php

namespace App\Domain\UseCases\CreateUser;

use App\Domain\Interfaces\UserEntity;

class CreateUserResponseModel
{
    public function __construct(
        private UserEntity $user
    ) {
    }

    public function getUser(): UserEntity
    {
        return $this->user;
    }
}
