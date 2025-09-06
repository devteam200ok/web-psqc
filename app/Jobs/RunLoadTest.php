<?php

// RunLoadTest.php
namespace App\Jobs;

use App\Services\LoadTestService;

class RunLoadTest extends BaseTestJob
{
    protected function getServiceClass(): string
    {
        return LoadTestService::class;
    }

    protected function getJobName(): string
    {
        return 'RunLoadTest';
    }
}