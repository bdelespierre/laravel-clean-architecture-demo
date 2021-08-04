<?php

namespace App\Domain\UseCases\CreateUser;

use App\Domain\Interfaces\InputPort;
use App\Domain\Interfaces\ViewModel;
use App\Models\EmailValueObject;
use App\Models\PasswordValueObject;
use App\Domain\UseCases\CreateUser\CreateUserRequestModel;

interface CreateUserInputPort
{
    public function createUser(CreateUserRequestModel $model): ViewModel;
}
