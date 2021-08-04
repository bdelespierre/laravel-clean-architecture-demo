<?php

namespace App\Domain\UseCases\CreateUser;

use App\Domain\Interfaces\OutputPort;
use App\Domain\Interfaces\UserEntity;
use App\Domain\Interfaces\ViewModel;
use App\Domain\UseCases\CreateUser\CreateUserResponseModel;

interface CreateUserOutputPort
{
    public function userCreated(CreateUserResponseModel $model): ViewModel;

    public function userAlreadyExists(CreateUserResponseModel $model): ViewModel;

    public function unableToCreateUser(CreateUserResponseModel $model, \Throwable $e): ViewModel;
}
