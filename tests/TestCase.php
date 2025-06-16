<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use App\Contracts\ResumeParser;
use Tests\Support\FakeResumeParser;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(ResumeParser::class, FakeResumeParser::class);
    }
}
