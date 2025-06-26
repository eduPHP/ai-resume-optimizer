<?php

namespace App\Http\Controllers;

use App\Services\JobCrawler;
use Illuminate\Http\JsonResponse;

class JobsController
{
    public function crawl(JobCrawler $crawler): JsonResponse
    {
        $crawler->loadJobInformation($url = request()->input('url'));

        return response()->json([
            'company' => $crawler->company,
            'position' => $crawler->title,
            'location' => $crawler->location,
            'url' => $url,
            'description' => $crawler->description,
        ]);
    }
}
