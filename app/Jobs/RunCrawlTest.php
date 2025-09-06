<?php
// RunCrawlTest.php
namespace App\Jobs;

use App\Services\CrawlTestService;

class RunCrawlTest extends BaseTestJob
{
    protected function getServiceClass(): string
    {
        return CrawlTestService::class;
    }

    protected function getJobName(): string
    {
        return 'RunCrawlTest';
    }
}