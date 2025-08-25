<?php

namespace App\Services\Crawler;

use App\Contracts\JobSiteCrawlerInterface;
use Illuminate\Support\Facades\Http;
use Closure;


class FireCrawlGLobalCrawler implements JobSiteCrawlerInterface
{
    public function shouldCrawl(string $url): bool
    {
        return true;
    }

    public function __invoke(JobCrawler $jobCrawler, Closure $next): JobCrawler
    {
        $data = cache()->remember(md5($jobCrawler->url), now()->addMonth(), function() use ($jobCrawler) {
            $response = Http::withToken(config('services.firecrawl.api_key'))->post('https://api.firecrawl.dev/v2/scrape', [
                'url' => $jobCrawler->url,
                'onlyMainContent' => true,
                'formats' => [
                    [
                        'type' => 'json',
                        'schema' => $this->getSchema(),
                    ]
                ],
            ]);

            if (! $response->successful()) {
                return $jobCrawler->toArray();
            }

            return [
                'title' => $response->json('data.json.role'),
                'company' => $response->json('data.json.company'),
                'location' => $response->json('data.json.location'),
                'description' => $response->json('data.json.description'),
            ];
        });

        $jobCrawler->setFromArray($data);
        $jobCrawler->processed = !! $jobCrawler->description;

        return $next($jobCrawler);
    }

    private function getSchema(): array
    {
        return [
            'type' => 'object',
            'requried' => [
                'company',
                'description',
                'role',
                'location',
                'url',
            ],
            'properties' => [
                'company' => [
                    'type' => 'string'
                ],
                'description' => [
                    'type' => 'string'
                ],
                'role' => [
                    'type' => 'string'
                ],
                'location' => [
                    'type' => 'string'
                ],
                'url' => [
                    'type' => 'string'
                ],
            ],
        ];
    }
}