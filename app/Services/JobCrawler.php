<?php

namespace App\Services;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class JobCrawler implements Arrayable
{

    public string $title;
    public string $company;
    public string $location;
    public string $description;

    public function loadJobInformation(string $url): static
    {
        $request = Http::get($url);

        $body = $request->body();

        $crawler = new Crawler($body);

        if (str($url)->contains('linkedin.com/jobs/view/')) {
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
