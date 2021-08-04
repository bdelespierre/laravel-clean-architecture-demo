<?php

namespace Tests\Unit;

use App\Adapters\ViewModels\CliViewModel;
use App\Adapters\Presenters\CreateUserCliPresenter;
use App\Adapters\Presenters\CreateUserHttpPresenter;
use App\Adapters\ViewModels\HttpResponseViewModel;
use App\Domain\Interfaces\UserEntity;
use App\Factories\UserModelFactory;
use App\Http\Controllers\CreateUserController;
use App\Http\Requests\CreateUserRequest;
use App\Models\EmailValueObject;
use App\Models\PasswordValueObject;
use App\Repositories\UserDatabaseRepository;
use App\Domain\UseCases\CreateUser\CreateUserInputPort;
use App\Domain\UseCases\CreateUser\CreateUserInteractor;
use App\Domain\UseCases\CreateUser\CreateUserResponseModel;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use Mockery;
use Tests\ManipulatesConfig;
use Tests\ProvidesUsers;
use Tests\TestCase;

class CreateUserAdaptersTest extends TestCase
{
    use RefreshDatabase;
    use ProvidesUsers;
    use ManipulatesConfig;

    /**
     * @dataProvider userDataProvider
     */
    public function testHttpPresenter(array $data)
    {
        $userMock = $this->mockUser($data);
        $responseModelMock = $this->mockCreateUserResponseModel($userMock);

        $presenter = new CreateUserHttpPresenter();

        // testing user created presentation
        $viewModel = $presenter->userCreated($responseModelMock);

        $this->assertInstanceof(HttpResponseViewModel::class, $viewModel);
        (new TestResponse($viewModel->getResponse()))->assertViewHas('user', $userMock);

        // testing user already exists presentation
        $viewModel = $presenter->userAlreadyExists($responseModelMock);

        $this->assertInstanceof(HttpResponseViewModel::class, $viewModel);
        (new TestResponse($viewModel->getResponse()))
            ->assertRedirect('/user/create')
            ->assertSessionHasErrors(['create-user' => "User {$userMock->getEmail()} alreay exists."]);

        // testing unexpected error presentation
        $viewModel = $this->usingConfig(
            ['app.debug' => false],
            fn() => $presenter->unableToCreateUser($responseModelMock, new \Exception("Error message"))
        );

        $this->assertInstanceof(HttpResponseViewModel::class, $viewModel);
        (new TestResponse($viewModel->getResponse()))
            ->assertRedirect('/user/create')
            ->assertSessionHasErrors(['create-user' => "Error occured while creating user {$userMock->getName()}"]);
    }

    /**
     * @dataProvider userDataProvider
     */
    public function testCliPresenter(array $data)
    {
        $userMock = $this->mockUser($data);
        $responseModelMock = $this->mockCreateUserResponseModel($userMock);

        $commandMock = tap(Mockery::mock(Command::class), function ($mock) use ($userMock, &$error) {
            $mock
                ->shouldReceive('info')->with("User {$userMock->getName()} successfully created.")
                ->shouldReceive('error')->with(Mockery::capture($error));
        });

        $presenter = new CreateUserCliPresenter();

        // testing user created presentation
        $viewModel = $presenter->userCreated($responseModelMock);

        $this->assertInstanceof(CliViewModel::class, $viewModel);
        $viewModel->handle($commandMock);

        // testing user already exists presentation
        $viewModel = $presenter->userAlreadyExists($responseModelMock);

        $this->assertInstanceof(CliViewModel::class, $viewModel);
        $viewModel->handle($commandMock);
        $this->assertEquals("User {$userMock->getEmail()} already exists!", $error);

        // testing unexpected error presentation
        $viewModel = $presenter->unableToCreateUser($responseModelMock, new \Exception("Error message"));

        $this->assertInstanceof(CliViewModel::class, $viewModel);
        $viewModel->handle($commandMock);
        $this->assertEquals("Error occured while creating user: Error message", $error);
    }

    /**
     * @dataProvider userDataProvider
     */
    public function testDatabaseRepository(array $data)
    {
        $userMock = $this->mockUser($data);
        $repository = new UserDatabaseRepository();

        $this->assertFalse($repository->exists($userMock));

        $user = $repository->create($userMock, new PasswordValueObject($data['email']));

        $this->assertTrue($user->exists);
        $this->assertTrue($repository->exists($userMock));
    }

    /**
     * @dataProvider userDataProvider
     */
    public function testModelFactory(array $data)
    {
        $factory = new UserModelFactory();
        $user = $factory->make($data);

        $this->assertUserMatches($data, $user);
    }

    /**
     * @dataProvider userDataProvider
     */
    public function testHttpEndToEnd(array $data)
    {
        $response = $this->post('user', $data);

        // expect to be on the show user view
        $response->assertViewIs('user.show');

        // expect the created user to be on the view
        $user = $response->viewData('user');
        $this->assertUserMatches($data, $user);

        // expect the user to be created in database
        $this->assertDatabaseHas('users', Arr::except($data, 'password'));

        // expect the user password (hashed) to validate against the
        // original password.
        $this->assertTrue($user->password->check(new PasswordValueObject($data['password'])));
    }

    /**
     * @dataProvider userDataProvider
     */
    public function testCliEndToEnd(array $data)
    {
        extract($data);

        $this->artisan('make:user', compact('name', 'email'))
            ->expectsQuestion('Password', $password)
            ->expectsQuestion('Confirm password', $password)
            ->expectsOutput("User {$name} successfully created.")
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', compact('name', 'email'));
    }

    private function mockUser(array $data): UserEntity
    {
        return tap(Mockery::mock(UserEntity::class), function ($mock) use ($data) {
            $mock
                ->shouldReceive('getName')->andReturn($data['name'])
                ->shouldReceive('getEmail')->andReturn(new EmailValueObject($data['email']))
                ->shouldNotReceive('getPassword');
        });
    }

    private function mockCreateUserResponseModel(UserEntity $user): CreateUserResponseModel
    {
        return tap(Mockery::mock(CreateUserResponseModel::class), function ($mock) use ($user) {
            $mock
                ->shouldReceive('getUser')
                ->andReturn($user);
        });
    }
}
