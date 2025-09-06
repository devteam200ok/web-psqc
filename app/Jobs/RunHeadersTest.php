<?php

// RunHeadersTest.php
namespace App\Jobs;

use App\Services\HeadersTestService;

class RunHeadersTest extends BaseTestJob
{
    protected function getServiceClass(): string
    {
        return HeadersTestService::class;
    }

    protected function getJobName(): string
    {
        return 'RunHeadersTest';
    }
}