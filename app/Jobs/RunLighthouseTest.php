<?php

// RunLighthouseTest.php
namespace App\Jobs;

use App\Services\LighthouseTestService;

class RunLighthouseTest extends BaseTestJob
{
    protected function getServiceClass(): string
    {
        return LighthouseTestService::class;
    }

    protected function getJobName(): string
    {
        return 'RunLighthouseTest';
    }
}