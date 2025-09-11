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
        if ($jobCrawler->processed) {
            return $next($jobCrawler);
        }

        $key = md5($jobCrawler->url);

        if (cache()->has("error-$key")) {
            cache()->forget($key);
        }

        $data = cache()->remember($key, now()->addMonth(), function() use ($jobCrawler, $key) {
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
                // cache busting flag when something went wrong
                cache()->remember("error-$key", now()->addMonth(), fn() => $response->body());

                return $jobCrawler->toArray();
            }

            $jobCrawler->processed = !! $response->json('data.json.is_job_page');

            return [
                'title' => $response->json('data.json.role'),
                'company' => $response->json('data.json.company'),
                'location' => $response->json('data.json.location'),
                'description' => $response->json('data.json.description'),
            ];
        });

        $jobCrawler->setFromArray($data);

        return $next($jobCrawler);
    }

    private function getSchema(): array
    {
        return [
            'type' => 'object',
            'required' => [
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
                'is_job_page' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Is this a job page?',
                ]
            ],
        ];
    }
}
