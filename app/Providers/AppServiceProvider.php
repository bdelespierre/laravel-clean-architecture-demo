<?php

namespace App\Providers;

use App\Adapters\Presenters;
use App\Console\Commands;
use App\Domain;
use App\Factories;
use App\Http\Controllers as HttpControllers;
use App\Http\Responses as HttpResponses;
use App\Repositories;
use App\Domain\UseCases;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            Domain\Interfaces\UserFactory::class,
            Factories\UserModelFactory::class,
        );

        $this->app->bind(
            Domain\Interfaces\UserRepository::class,
            Repositories\UserDatabaseRepository::class,
        );

        $this->app
            ->when(HttpControllers\CreateUserController::class)
            ->needs(UseCases\CreateUser\CreateUserInputPort::class)
            ->give(function ($app) {
                return $app->make(UseCases\CreateUser\CreateUserInteractor::class, [
                    'output' => $app->make(Presenters\CreateUserHttpPresenter::class),
                ]);
            });

        $this->app
            ->when(Commands\CreateUserCommand::class)
            ->needs(UseCases\CreateUser\CreateUserInputPort::class)
            ->give(function ($app) {
                return $app->make(UseCases\CreateUser\CreateUserInteractor::class, [
                    'output' => $app->make(Presenters\CreateUserCliPresenter::class),
                ]);
            });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
