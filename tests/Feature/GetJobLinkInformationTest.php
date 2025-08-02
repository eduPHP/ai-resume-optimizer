<?php

use Illuminate\Support\Facades\Http;

test('it imports the job information from linkedin', function () {
    Http::preventStrayRequests();
    Http::fake([
        'https://www.linkedin.com/jobs/view/*' => Http::response(
            file_get_contents(__DIR__ . '/../Fixtures/linkedin-sample.html')
        ),
    ]);

    $service = new \App\Services\JobCrawler();

    $service->crawl('https://www.linkedin.com/jobs/view/4253350439/?alternateChannel=search&refId=wNitVig4Rl27bNGUPyfT2A%3D%3D&trackingId=fWHtBUEA4EN3G1KTMm7Bvw%3D%3D');
    $service->loadJobInformation();

    expect($service->toArray())->toHaveCount(5);
    expect($service->title)->toBe('Full Stack Developer');
    expect($service->company)->toBe('The Patrick J. McGovern Foundation');
    expect($service->location)->toBe('United States');
    expect($service->description)->toContain('Proficiency in JavaScript');
    expect($service->url)->toContain('https://www.linkedin.com/jobs/view/4253350439/');
});

