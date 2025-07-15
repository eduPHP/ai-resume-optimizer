<?php

namespace App\Services;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class JobCrawler implements Arrayable
{
    public ?string $title = null;
    public ?string $company = null;
    public ?string $location = null;
    public ?string $description = null;
    public string $url;

    public function crawl(string $url): static
    {
        $this->url = $url;

        $this->loadJobInformation();

        return $this;
    }

    public function isSupported(): bool
    {
        return str($this->url)->contains('linkedin.com/jobs/view/');
    }

    public function loadJobInformation(): static
    {
        if ( ! $this->isSupported()) {
            return $this;
        }

        $request = Http::get($this->url);

        $body = $request->body();

        $crawler = new Crawler($body);

        if (str($this->url)->contains('linkedin.com/jobs/view/')) {
            $this->url = str($this->url)->before('?'); // cleanup url
            $this->linkedin($crawler);
        }

//        if (str($url)->contains('indeed.com/viewjob?')) {
//            $this->indeed($crawler);
//        }


        return $this;
    }


    public function toArray(): array
    {
        return get_object_vars($this);
    }

    private function linkedin(Crawler $crawler): void
    {
        $this->title = $crawler
            ->filter('.topcard__title')
            ->first()
            ->text();

        $this->company = $crawler
            ->filter('.sub-nav-cta__optional-url')
            ->first()
            ->text();

        $this->location = $crawler
            ->filter('.sub-nav-cta__meta-text')
            ->first()
            ->text();

        $this->description = $crawler
            ->filter('.show-more-less-html__markup')
            ->first()
            ->text();
    }
}
