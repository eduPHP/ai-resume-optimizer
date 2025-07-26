<?php

namespace Tests\Feature;

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetJobLinkInformationTest extends TestCase
{
    #[Test]
    public function it_imports_the_job_information_from_linkedin()
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://www.linkedin.com/jobs/view/*' => Http::response(
                file_get_contents(__DIR__.'/../Fixtures/linkedin-sample.html')
            ),
        ]);

        $service = new \App\Services\JobCrawler();

        $service->crawl('https://www.linkedin.com/jobs/view/4253350439/?alternateChannel=search&refId=wNitVig4Rl27bNGUPyfT2A%3D%3D&trackingId=fWHtBUEA4EN3G1KTMm7Bvw%3D%3D');
        $service->loadJobInformation();


        $this->assertCount(5, $service->toArray());
        $this->assertEquals('Full Stack Developer', $service->title);
        $this->assertEquals('The Patrick J. McGovern Foundation', $service->company);
        $this->assertEquals('United States', $service->location);
        $this->assertStringContainsString('Proficiency in JavaScript', $service->description);
        $this->assertStringContainsString('https://www.linkedin.com/jobs/view/4253350439/', $service->url);
    }

//    #[Test]
//    public function it_imports_the_job_information_from_indeed()
//    {
//        Http::preventStrayRequests();
//        Http::fake([
//            'https://www.indeed.com/viewjob?jk=4253350439&from=serp&vjk=4253350439' => Http::response(
//                file_get_contents(__DIR__.'/../Fixtures/indeed-sample.html')
//            ),
//        ]);

//        $service = new \App\Services\JobCrawler();
//        $service->loadJobInformation('https://www.indeed.com/viewjob?from=appshareios&jk=3425b0b71e806c60');
//        $this->assertCount(4, $service->toArray());
//        $this->assertEquals('Full Stack Developer', $service->title);
//        $this->assertEquals('The Patrick J. McGovern Foundation', $service->company);
//        $this->assertEquals('United States', $service->location);
//        $this->assertStringContainsString('Proficiency in JavaScript', $service->description);
//    }

}
