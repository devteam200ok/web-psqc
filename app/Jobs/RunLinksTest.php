<?php

// RunLinksTest.php
namespace App\Jobs;

use App\Services\LinksTestService;

class RunLinksTest extends BaseTestJob
{
    protected function getServiceClass(): string
    {
        return LinksTestService::class;
    }

    protected function getJobName(): string
    {
        return 'RunLinksTest';
    }
}