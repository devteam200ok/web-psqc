<?php
// RunSpeedTest.php
namespace App\Jobs;

use App\Services\SpeedTestService;

class RunSpeedTest extends BaseTestJob
{
    protected function getServiceClass(): string
    {
        return SpeedTestService::class;
    }

    protected function getJobName(): string
    {
        return 'RunSpeedTest';
    }
}