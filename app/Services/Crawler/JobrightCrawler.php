<?php
namespace App\Services\Crawler;

use App\Contracts\JobSiteCrawlerInterface;
use Closure;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class JobrightCrawler implements JobSiteCrawlerInterface
{
    public function shouldCrawl(string $url): bool
    {
        return str($url)->contains('');
    }

    public function __invoke(JobCrawler $jobCrawler, Closure $next): JobCrawler
    {
        if (! $this->shouldCrawl($jobCrawler->url)) {
            return $next($jobCrawler);
        }
        $crawler = new Crawler($this->getContent($jobCrawler->url));

        $nextData = $crawler->filter('#__NEXT_DATA__')->first()->text();

        $data = json_decode($nextData);

        dd($data);

    }

    
    private function getContent(string $url): string
    {
        $request = Http::get($url);

        return $request->body();
    }
}