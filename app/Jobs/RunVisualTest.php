<?php

// RunVisualTest.php
namespace App\Jobs;

use App\Services\VisualTestService;

class RunVisualTest extends BaseTestJob
{
    protected function getServiceClass(): string
    {
        return VisualTestService::class;
    }

    protected function getJobName(): string
    {
        return 'RunVisualTest';
    }
}