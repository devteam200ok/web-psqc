<?php

// RunMobileTest.php
namespace App\Jobs;

use App\Services\MobileTestService;

class RunMobileTest extends BaseTestJob
{
    protected function getServiceClass(): string
    {
        return MobileTestService::class;
    }

    protected function getJobName(): string
    {
        return 'RunMobileTest';
    }
}