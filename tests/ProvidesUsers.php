<?php

namespace Tests;

use App\Domain\Interfaces\UserEntity;
use App\Models\EmailValueObject;
use App\Models\PasswordValueObject;
use App\Domain\UseCases\CreateUser\CreateUserResponseModel;
use Mockery;

trait ProvidesUsers
{
    public function userDataProvider()
    {
        return [
            'John DOE' => [
                'data' => [
                    'name' => "John DOE",
                    'email' => "john.doe@example.com",
                    'password' => "B1ggb055",
                ],
            ],
        ];
    }

    public function assertUserMatches(array $data, UserEntity $user)
    {
        $this->assertEquals($data['name'], $user->getName());
        $this->assertTrue($user->getEmail()->isEqualTo(new EmailValueObject($data['email'])));
        $this->assertTrue($user->getPassword()->check(new PasswordValueObject($data['password'])));
    }
}
