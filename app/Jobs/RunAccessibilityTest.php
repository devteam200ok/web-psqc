<?php
// RunAccessibilityTest.php
namespace App\Jobs;

use App\Services\AccessibilityTestService;

class RunAccessibilityTest extends BaseTestJob
{
    protected function getServiceClass(): string
    {
        return AccessibilityTestService::class;
    }

    protected function getJobName(): string
    {
        return 'RunAccessibilityTest';
    }
}