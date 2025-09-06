<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Models\Certificate;
use App\Models\WebTest;

#[AsCommand(
    name: 'cert:make-pdf',
    description: 'Capture the printable certificate page to PDF and save to disk(local)/certification/{code}.pdf'
)]
class CertMakePdf extends Command
{
    protected function configure(): void
    {
        $this->addArgument('code', InputArgument::REQUIRED, 'Certificate code');
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite if file exists');
    }

    public function handle(): int
    {
        $code = (string) $this->argument('code');

        $certificate = Certificate::where('code', $code)->first();
        if (!$certificate) { $this->error("Certificate not found: {$code}"); return self::FAILURE; }
        if ($certificate->payment_status !== 'paid') {
            $this->error("Certificate not paid (status={$certificate->payment_status})");
            return self::FAILURE;
        }
        $currentTest = WebTest::find($certificate->web_test_id);
        if (!$currentTest) { $this->error("WebTest not found: {$certificate->web_test_id}"); return self::FAILURE; }

        // 저장 경로
        $disk = Storage::disk('local');          // root = storage/app/private
        $dir  = 'certification';
        $rel  = "{$dir}/{$code}.pdf";
        $abs  = $disk->path($rel);

        if ($disk->exists($rel) && !$this->option('force')) {
            $this->warn("Already exists: storage/app/private/{$rel} (use --force to overwrite)");
            return self::SUCCESS;
        }
        $disk->makeDirectory($dir);

        // ✅ 실제 페이지 URL을 캡처 (레이아웃/섹션/CSS 전부 적용)
        $url = route('certificates.pdf', ['code' => $code]);

        $b = Browsershot::url($url)
            ->emulateMedia('screen')
            ->format('A4')
            ->margins(8, 8, 10, 8)
            ->windowSize(1280, 1800)
            ->scale(0.90) // ← 여기서만 축소 적용 (0.88~0.92 사이에서 미세 조정)
            ->showBackground()
            ->timeout(120)
            ->setOption('args', ['--no-sandbox','--disable-setuid-sandbox']);

        if (env('BROWSERSHOT_NODE_PATH'))   $b->setNodeBinary(env('BROWSERSHOT_NODE_PATH'));
        if (env('BROWSERSHOT_NPM_PATH'))    $b->setNpmBinary(env('BROWSERSHOT_NPM_PATH'));
        if (env('BROWSERSHOT_CHROME_PATH')) $b->setChromePath(env('BROWSERSHOT_CHROME_PATH'));

        try {
            $b->savePdf($abs);
            $this->info("✅ Saved: storage/app/private/{$rel}");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            try {
                $disk->put($rel, $b->pdf());
                $this->warn('Direct save failed, fallback (bytes→Storage) succeeded.');
                $this->info("✅ Saved: storage/app/private/{$rel}");
                return self::SUCCESS;
            } catch (\Throwable $inner) {
                $this->error('Browsershot failed: '.$e->getMessage());
                $this->error('Fallback failed: '.$inner->getMessage());
                return self::FAILURE;
            }
        }
    }
}