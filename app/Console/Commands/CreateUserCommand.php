<?php

namespace App\Console\Commands;

use App\Adapters\ViewModels\CliViewModel;
use App\Domain\UseCases\CreateUser\CreateUserInputPort;
use App\Domain\UseCases\CreateUser\CreateUserRequestModel;
use Illuminate\Console\Command;

class CreateUserCommand extends Command
{
    protected $signature = 'make:user {name} {email}';

    protected $description = 'Creates an user';

    public function __construct(
        private CreateUserInputPort $interactor
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $password = $this->ask('Password');
        $confirm = $this->ask('Confirm password');

        if ($password != $confirm) {
            $this->error("Password confirmation doesn't match.");
            return self::FAILURE;
        }

        $viewModel = $this->interactor->createUser(
            new CreateUserRequestModel([
                'name' => $this->argument('name'),
                'email' => $this->argument('email'),
                'password' => $password,
            ])
        );

        if ($viewModel instanceof CliViewModel) {
            return $viewModel->handle($this);
        }

        return self::SUCCESS;
    }
}
