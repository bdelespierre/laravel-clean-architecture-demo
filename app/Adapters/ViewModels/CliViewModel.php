<?php

namespace App\Adapters\ViewModels;

use App\Domain\Interfaces\ViewModel;
use Illuminate\Console\Command;

class CliViewModel implements ViewModel
{
    public function __construct(
        private \Closure $handler
    ) {
    }

    public function handle(Command $command): mixed
    {
        return $this->handler->call($command, $command);
    }
}
