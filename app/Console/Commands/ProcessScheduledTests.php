<?php

namespace App\Console\Commands;

use App\Models\ScheduledTest;
use App\Models\WebTest;
use App\Jobs\RunSpeedTest;
use App\Jobs\RunLoadTest;
use App\Jobs\RunMobileTest;
use App\Jobs\RunSslTest;
use App\Jobs\RunSslyzeTest;
use App\Jobs\RunHeadersTest;
use App\Jobs\RunScanTest;
use App\Jobs\RunNucleiTest;
use App\Jobs\RunLighthouseTest;
use App\Jobs\RunAccessibilityTest;
use App\Jobs\RunCompatibilityTest;
use App\Jobs\RunVisualTest;
use App\Jobs\RunLinksTest;
use App\Jobs\RunStructureTest;
use App\Jobs\RunCrawlTest;
use App\Jobs\RunMetaTest;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessScheduledTests extends Command
{
    protected $signature = 'tests:process-scheduled {--limit=50 : Maximum number of tests to process} {--dry-run : Show what would be processed without executing}';
    
    protected $description = 'Process scheduled tests that are due for execution';

    public function handle()
    {
        $limit = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');
        
        $this->info("Processing scheduled tests...");
        if ($dryRun) {
            $this->warn("DRY RUN MODE - No tests will actually be executed");
        }
        
        // 실행 대기중인 예약된 테스트 조회
        $scheduledTests = ScheduledTest::pending()
            ->due()
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get();
            
        if ($scheduledTests->isEmpty()) {
            $this->info("No scheduled tests due for execution.");
            return 0;
        }
        
        $this->info("Found {$scheduledTests->count()} scheduled tests to process.");
        
        $processed = 0;
        $failed = 0;
        
        foreach ($scheduledTests as $scheduledTest) {
            try {
                $this->line("Processing: {$scheduledTest->test_type} for {$scheduledTest->short_domain}");
                
                if ($dryRun) {
                    $this->line("  [DRY RUN] Would execute at: {$scheduledTest->scheduled_at}");
                    continue;
                }
                
                // WebTest 생성
                $webTest = WebTest::create([
                    'user_id' => $scheduledTest->user_id,
                    'test_type' => $scheduledTest->test_type,
                    'url' => $scheduledTest->url,
                    'status' => 'pending',
                    'started_at' => now(),
                    'test_config' => array_merge(
                        $scheduledTest->test_config ?? [],
                        ['scheduled_from' => $scheduledTest->id]
                    )
                ]);
                
                // 테스트 타입별로 적절한 Job 실행
                $dispatched = $this->dispatchTestJob($scheduledTest->test_type, $scheduledTest->url, $webTest->id);
                
                if ($dispatched) {
                    // 예약된 테스트를 실행됨으로 표시
                    $scheduledTest->markAsExecuted($webTest->id);
                    $processed++;
                    
                    $this->line("  ✓ Dispatched successfully (WebTest ID: {$webTest->id})");
                    
                    Log::info('Scheduled test executed', [
                        'scheduled_test_id' => $scheduledTest->id,
                        'web_test_id' => $webTest->id,
                        'test_type' => $scheduledTest->test_type,
                        'url' => $scheduledTest->url
                    ]);
                } else {
                    // 지원하지 않는 테스트 타입
                    $scheduledTest->markAsFailed("Unsupported test type: {$scheduledTest->test_type}");
                    $webTest->update(['status' => 'failed', 'error_message' => 'Unsupported test type']);
                    $failed++;
                    
                    $this->error("  ✗ Unsupported test type: {$scheduledTest->test_type}");
                }
                
            } catch (\Exception $e) {
                $failed++;
                $scheduledTest->markAsFailed($e->getMessage());
                
                $this->error("  ✗ Failed: {$e->getMessage()}");
                
                Log::error('Scheduled test execution failed', [
                    'scheduled_test_id' => $scheduledTest->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        $this->info("\nProcessing completed:");
        $this->line("  - Processed: {$processed}");
        $this->line("  - Failed: {$failed}");
        
        // 오래된 스케줄 정리 (옵션)
        if (!$dryRun && $processed > 0) {
            $this->cleanupOldSchedules();
        }
        
        return 0;
    }
    
    private function dispatchTestJob(string $testType, string $url, int $webTestId): bool
    {
        try {
            $handled = match ($testType) {
                'p-speed' => (bool) RunSpeedTest::dispatch($url, $webTestId)->onQueue('speed'),
                'p-load' => (bool) RunLoadTest::dispatch($url, $webTestId)->onQueue('load'),
                'p-mobile' => (bool) RunMobileTest::dispatch($url, $webTestId)->onQueue('mobile'),

                's-ssl' => (bool) RunSslTest::dispatch($url, $webTestId)->onQueue('ssl'),
                's-sslyze' => (bool) RunSslyzeTest::dispatch($url, $webTestId)->onQueue('sslyze'),
                's-header' => (bool) RunHeadersTest::dispatch($url, $webTestId)->onQueue('headers'),
                's-scan' => (bool) RunScanTest::dispatch($url, $webTestId)->onQueue('scan'),
                's-nuclei' => (bool) RunNucleiTest::dispatch($url, $webTestId)->onQueue('nuclei'),

                'q-lighthouse' => (bool) RunLighthouseTest::dispatch($url, $webTestId)->onQueue('lighthouse'),
                'q-accessibility' => (bool) RunAccessibilityTest::dispatch($url, $webTestId)->onQueue('accessibility'),
                'q-compatibility' => (bool) RunCompatibilityTest::dispatch($url, $webTestId)->onQueue('compatibility'),
                'q-visual' => (bool) RunVisualTest::dispatch($url, $webTestId)->onQueue('visual'),

                'c-links' => (bool) RunLinksTest::dispatch($url, $webTestId)->onQueue('links'),
                'c-structure' => (bool) RunStructureTest::dispatch($url, $webTestId)->onQueue('structure'),
                'c-crawl' => (bool) RunCrawlTest::dispatch($url, $webTestId)->onQueue('crawl'),
                'c-meta' => (bool) RunMetaTest::dispatch($url, $webTestId)->onQueue('meta'),
                default => false
            };

            return (bool) $handled;
        } catch (\Exception $e) {
            Log::error('Failed to dispatch test job', [
                'test_type' => $testType,
                'url' => $url,
                'web_test_id' => $webTestId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    private function cleanupOldSchedules(): void
    {
        try {
            $deleted = ScheduledTest::cleanupOldSchedules(30); // 30일 이상 된 완료/실패/취소된 스케줄 삭제
            
            if ($deleted > 0) {
                $this->line("Cleaned up {$deleted} old scheduled tests.");
            }
        } catch (\Exception $e) {
            $this->error("Failed to cleanup old schedules: " . $e->getMessage());
        }
    }
}
