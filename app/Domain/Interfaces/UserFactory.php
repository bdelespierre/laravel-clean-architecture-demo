<?php

namespace App\Domain\Interfaces;

interface UserFactory
{
    /**
     * @param array<mixed> $attributes
     */
    public function make(array $attributes = []): UserEntity;
}
