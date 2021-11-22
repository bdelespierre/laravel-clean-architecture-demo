<?php

namespace App\Adapters\Presenters;

use App\Adapters\ViewModels\JsonResourceViewModel;
use App\Domain\Interfaces\ViewModel;
use App\Domain\UseCases\CreateUser\CreateUserOutputPort;
use App\Domain\UseCases\CreateUser\CreateUserResponseModel;
use App\Http\Resources\UnableToCreateUserResource;
use App\Http\Resources\UserAlreadyExistsResource;
use App\Http\Resources\UserCreatedResource;

class CreateUserJsonPresenter implements CreateUserOutputPort
{
    public function userCreated(CreateUserResponseModel $model): ViewModel
    {
        return new JsonResourceViewModel(
            new UserCreatedResource($model->getUser())
        );
    }

    public function userAlreadyExists(CreateUserResponseModel $model): ViewModel
    {
        return new JsonResourceViewModel(
            new UserAlreadyExistsResource($model->getUser())
        );
    }

    public function unableToCreateUser(CreateUserResponseModel $model, \Throwable $e): ViewModel
    {
        if (config('app.debug')) {
            // rethrow and let Laravel display the error
            throw $e;
        }

        return new JsonResourceViewModel(
            new UnableToCreateUserResource($e)
        );
    }
}
