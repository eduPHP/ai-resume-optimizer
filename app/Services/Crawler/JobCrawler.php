<?php

namespace App\Services\Crawler;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Contracts\Support\Arrayable;

class JobCrawler implements Arrayable
{
    public ?string $title = null;
    public ?string $company = null;
    public ?string $location = null;
    public ?string $description = null;
    public string $url;
    public bool $processed = false;

    public function crawl(string $url): static
    {
        $this->url = $url;

        return app(Pipeline::class)
            ->send($this)
            ->through([
                LinkedinCrawler::class,
                JobrightCrawler::class,
            ])->thenReturn();
    }

    public function isSupported(): bool
    {
        return $this->processed;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'company' => $this->company,
            'location' => $this->location,
            'description' => $this->description,
        ];
    }
}
