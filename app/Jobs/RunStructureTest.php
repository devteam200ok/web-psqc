<?php

// RunStructureTest.php
namespace App\Jobs;

use App\Services\StructureTestService;

class RunStructureTest extends BaseTestJob
{
    protected function getServiceClass(): string
    {
        return StructureTestService::class;
    }

    protected function getJobName(): string
    {
        return 'RunStructureTest';
    }
}