<?php

namespace App\Http\Controllers;

use App\Services\JobCrawler;
use Illuminate\Http\JsonResponse;

class JobsController
{
    public function crawl(JobCrawler $crawler): JsonResponse
    {
        $url = request()->input('url');

        if (! $crawler->isSupported($url)) {
            return response()->json([
                'supported' => false,
                'company' => null,
                'position' => null,
                'location' => null,
                'url' => $url,
                'description' => null,
            ]);
        }

        $crawler->loadJobInformation($url);

        return response()->json([
            'company' => $crawler->company,
            'position' => $crawler->title,
            'location' => $crawler->location,
            'url' => $url,
            'description' => $crawler->description,
        ]);
    }
}
