<?php
namespace App\Contracts;

interface ResumeParser
{
    public function getText(string $path): string;
}
