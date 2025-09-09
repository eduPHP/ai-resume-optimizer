<?php

namespace App\Http\Controllers;

use App\Jobs\OptimizeResume;
use App\Services\Crawler\JobCrawler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobsController
{
    use CreateUnattendedOptimization, PresentOptimization;

    public function crawl(JobCrawler $crawler): JsonResponse
    {
        request()->validate(['url' => 'required|url']);

        return response()->json($this->doCrawl($crawler, request()->input('url')));
    }

    public function unattended(Request $request, JobCrawler $crawler)
    {
        $text = $request->input('message.body');
        if (preg_match('/https?:\/\/\S+/', $text, $matches)) {
            $data = $this->doCrawl($crawler, $matches[0]);

            if ($data['supported'] ?? false) {
                if (! $resume = auth()->user()->resumes()->latest()->first()) {
                    return response()->json([
                        'supported' => false,
                        'error' => config('setup.errors.resume_not_found'),
                    ], 422);
                }


                $optimization = $this->createOptimizationFor($resume, collect([
                    ...$data,
                    'name' => $data['position'],
                ]));

                return response()->json([
                    'supported' => true,
                    'optimization' => $this->presentForListing($optimization),
                ]);
            }

            return response()->json($data);
        }

        return response()->json([
            'supported' => false,
            'error' => 'No url provided',
        ], 422);
    }

    private function doCrawl(JobCrawler $crawler, string $url): array
    {
        $key = str($url)->slug()->hash('sha1');

        return cache()->remember('crawl-' . $key, now()->addDay(), function () use ($crawler, $url): array {
            $crawler->crawl($url);

            return [
                'supported' => $crawler->isSupported(),
                'company' => $crawler->company,
                'position' => $crawler->title,
                'location' => $crawler->location,
                'url' => $crawler->url,
                'description' => $crawler->description,
            ];
        });
    }
}
