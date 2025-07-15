<?php

namespace App\Http\Controllers;

use App\Services\JobCrawler;
use Illuminate\Http\JsonResponse;

class JobsController
{
    public function crawl(JobCrawler $crawler): JsonResponse
    {
        request()->validate(['url' => 'required|url']);

        $crawler->crawl(request()->input('url'));

        return response()->json([
            'supported' => $crawler->isSupported(),
            'company' => $crawler->company,
            'position' => $crawler->title,
            'location' => $crawler->location,
            'url' => $crawler->url,
            'description' => $crawler->description,
        ]);
    }
}
