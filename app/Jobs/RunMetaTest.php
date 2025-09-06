<?php

// RunMetaTest.php
namespace App\Jobs;

use App\Services\MetaTestService;

class RunMetaTest extends BaseTestJob
{
    protected function getServiceClass(): string
    {
        return MetaTestService::class;
    }

    protected function getJobName(): string
    {
        return 'RunMetaTest';
    }
}