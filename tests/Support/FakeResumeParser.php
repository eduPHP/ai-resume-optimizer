<?php
namespace Tests\Support;

use App\Contracts\ResumeParser;

class FakeResumeParser implements ResumeParser
{
    public function getText(string $path): string
    {
        return file_get_contents(__DIR__.'/../Fixtures/resume-example.txt');
    }
}
