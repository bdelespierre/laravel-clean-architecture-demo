<?php

namespace App\Http\Controllers;

use App\Adapters\ViewModels\HttpResponseViewModel;
use App\Domain\UseCases\CreateUser\CreateUserInputPort;
use App\Domain\UseCases\CreateUser\CreateUserRequestModel;
use App\Http\Requests\CreateUserRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class CreateUserController extends Controller
{
    public function __construct(
        private CreateUserInputPort $interactor,
    ) {
    }

    public function __invoke(CreateUserRequest $request): ?HttpResponse
    {
        $viewModel = $this->interactor->createUser(
            new CreateUserRequestModel($request->validated())
        );

        // we can't force the interactor to return an HttpResponseViewModel
        // so we need a simple check (or PHPStan will yell)
        if ($viewModel instanceof HttpResponseViewModel) {
            return $viewModel->getResponse();
        }

        return null;
    }
}
