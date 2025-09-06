<?php
// RunScanTest.php - timeout이 다른 경우
namespace App\Jobs;

use App\Services\ScanTestService;

class RunScanTest extends BaseTestJob
{
    public $timeout = 150; // Override default timeout

    protected function getServiceClass(): string
    {
        return ScanTestService::class;
    }

    protected function getJobName(): string
    {
        return 'RunScanTest';
    }
}