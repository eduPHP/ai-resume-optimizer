<?php

namespace App\Http\Controllers;

use App\Services\Crawler\JobCrawler;
use Illuminate\Http\JsonResponse;

class JobsController
{
    public function crawl(JobCrawler $crawler): JsonResponse
    {
        request()->validate(['url' => 'required|url']);

        return cache()->remember('crawl-'.str(request()->input('url'))->slug(), now()->addDay(), function () use ($crawler) {
            $crawler->crawl(request()->input('url'));

            return response()->json([
                'supported' => $crawler->isSupported(),
                'company' => $crawler->company,
                'position' => $crawler->title,
                'location' => $crawler->location,
                'url' => $crawler->url,
                'description' => $crawler->description,
            ]);
        });
    }
}
