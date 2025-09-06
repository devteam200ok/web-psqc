<?php

// RunSslTest.php
namespace App\Jobs;

use App\Services\SslTestService;

class RunSslTest extends BaseTestJob
{
    protected function getServiceClass(): string
    {
        return SslTestService::class;
    }

    protected function getJobName(): string
    {
        return 'RunSslTest';
    }
}