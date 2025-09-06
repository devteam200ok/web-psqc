<?php

// RunSslyzeTest.php
namespace App\Jobs;

use App\Services\SslyzeTestService;

class RunSslyzeTest extends BaseTestJob
{
    protected function getServiceClass(): string
    {
        return SslyzeTestService::class;
    }

    protected function getJobName(): string
    {
        return 'RunSslyzeTest';
    }
}