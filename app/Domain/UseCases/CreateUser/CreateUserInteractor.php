<?php

namespace App\Domain\UseCases\CreateUser;

use App\Domain\Interfaces\UserEntity;
use App\Domain\Interfaces\UserFactory;
use App\Domain\Interfaces\UserRepository;
use App\Domain\Interfaces\ViewModel;
use App\Models\PasswordValueObject;
use App\Domain\UseCases\CreateUser\CreateUserInputPort;

class CreateUserInteractor implements CreateUserInputPort
{
    public function __construct(
        private CreateUserOutputPort $output,
        private UserRepository $repository,
        private UserFactory $factory,
    ) {
    }

    public function createUser(CreateUserRequestModel $request): ViewModel
    {
        $user = $this->factory->make([
            'name' => $request->getName(),
            'email' => $request->getEmail(),
        ]);

        if ($this->repository->exists($user)) {
            return $this->output->userAlreadyExists(new CreateUserResponseModel($user));
        }

        try {
            $user = $this->repository->create($user, new PasswordValueObject($request->getPassword()));
        } catch (\Exception $e) {
            return $this->output->unableToCreateUser(new CreateUserResponseModel($user), $e);
        }

        return $this->output->userCreated(
            new CreateUserResponseModel($user)
        );
    }
}
