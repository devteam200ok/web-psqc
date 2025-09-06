<?php
// RunCompatibilityTest.php
namespace App\Jobs;

use App\Services\CompatibilityTestService;

class RunCompatibilityTest extends BaseTestJob
{
    protected function getServiceClass(): string
    {
        return CompatibilityTestService::class;
    }

    protected function getJobName(): string
    {
        return 'RunCompatibilityTest';
    }
}