<?php

namespace App\DTO\Contracts;

interface AIInputOptions
{
    public function system(): array;
    public function user(): array;
}
