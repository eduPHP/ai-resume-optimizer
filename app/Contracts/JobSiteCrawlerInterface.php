<?php

namespace App\Contracts;

use App\Services\Crawler\JobCrawler;
use Closure;

interface JobSiteCrawlerInterface
{
    public function shouldCrawl(string $url): bool;
    public function __invoke(JobCrawler $jobCrawler, Closure $next): JobCrawler;
}