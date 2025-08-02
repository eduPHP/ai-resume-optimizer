<?php

namespace App\Services\Crawler;

use App\Contracts\JobSiteCrawlerInterface;
use App\Services\Crawler\JobCrawler;
use Closure;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class LinkedinCrawler implements JobSiteCrawlerInterface
{
    public function shouldCrawl(string $url): bool
    {
        return str($url)->contains('linkedin.com/jobs/view/');
    }

    public function __invoke(JobCrawler $jobCrawler, Closure $next): JobCrawler
    {
        if (! $this->shouldCrawl($jobCrawler->url)) {
            return $next($jobCrawler);
        }

        $jobCrawler->url = str($jobCrawler->url)->before('?'); // cleanup url

        $crawler = new Crawler($this->getContent($jobCrawler->url));

        $jobCrawler->title = $crawler
            ->filter('.topcard__title')
            ->first()
            ->text();

        $jobCrawler->company = $crawler
            ->filter('.sub-nav-cta__optional-url')
            ->first()
            ->text();

        $jobCrawler->location = $crawler
            ->filter('.sub-nav-cta__meta-text')
            ->first()
            ->text();

        $jobCrawler->description = $crawler
            ->filter('.show-more-less-html__markup')
            ->first()
            ->text();

        $jobCrawler->processed = true;

        return $next($jobCrawler);
    }

    private function getContent(string $url): string
    {
        $request = Http::get($url);

        return $request->body();
    }
}
