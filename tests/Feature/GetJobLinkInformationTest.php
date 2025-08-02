<?php

use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();
});

test('it gracefully handles links that are not supported', function () {
    $service = new \App\Services\Crawler\JobCrawler();

    $service->crawl('https://www.some-jobs-board.com/job/123456789');

    expect($service->toArray())->toHaveCount(4)
        ->and($service->title)->toBeNull()
        ->and($service->company)->toBeNull()
        ->and($service->location)->toBeNull()
        ->and($service->description)->toBeNull()
        ->and($service->url)->toContain('https://www.some-jobs-board.com/job/123456789')
        ->and($service->isSupported())->toBeFalse();
});

test('it imports the job information from linkedin', function () {
    Http::fake([
        'https://www.linkedin.com/jobs/view/*' => Http::response(
            file_get_contents(__DIR__ . '/../Fixtures/linkedin-sample.html')
        ),
    ]);

    $service = new \App\Services\Crawler\JobCrawler();

    $service->crawl('https://www.linkedin.com/jobs/view/4253350439/?alternateChannel=search&refId=wNitVig4Rl27bNGUPyfT2A%3D%3D&trackingId=fWHtBUEA4EN3G1KTMm7Bvw%3D%3D');

    expect($service->isSupported())->toBeTrue()
        ->and($service->toArray())->toHaveCount(4)
        ->and($service->title)->toBe('Full Stack Developer')
        ->and($service->company)->toBe('The Patrick J. McGovern Foundation')
        ->and($service->location)->toBe('United States')
        ->and($service->description)->toContain('Proficiency in JavaScript')
        ->and($service->url)->toContain('https://www.linkedin.com/jobs/view/4253350439/');
});

it('imports the job information from jobright', function() {
    Http::fake([
        'https://www.linkedin.com/jobs/view/*' => Http::response(
            file_get_contents(__DIR__.'/../Fixtures/linkedin-sample.html')
        ),
    ]);

    $service = new \App\Services\Crawler\JobCrawler();

    $service->crawl('https://www.linkedin.com/jobs/view/4253350439/?alternateChannel=search&refId=wNitVig4Rl27bNGUPyfT2A%3D%3D&trackingId=fWHtBUEA4EN3G1KTMm7Bvw%3D%3D');

    expect($service->isSupported())->toBeTrue()
        ->and($service->toArray())->toHaveCount(4)
        ->and($service->title)->toBe('Full Stack Developer')
        ->and($service->company)->toBe('The Patrick J. McGovern Foundation')
        ->and($service->location)->toBe('United States')
        ->and($service->description)->toContain('Proficiency in JavaScript')
        ->and($service->url)->toBe('https://www.linkedin.com/jobs/view/4253350439/');
});
