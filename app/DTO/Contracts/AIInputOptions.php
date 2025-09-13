<?php

namespace App\DTO\Contracts;

interface AIInputOptions
{
    public function metadata(): array;

    public function system(): array;

    public function user(): array;

    public function schema(): array;
}
