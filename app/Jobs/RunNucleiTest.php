<?php

// RunNucleiTest.php - timeout이 다른 경우
namespace App\Jobs;

use App\Services\NucleiTestService;

class RunNucleiTest extends BaseTestJob
{
    public $timeout = 180; // Override default timeout

    protected function getServiceClass(): string
    {
        return NucleiTestService::class;
    }

    protected function getJobName(): string
    {
        return 'RunNucleiTest';
    }
}