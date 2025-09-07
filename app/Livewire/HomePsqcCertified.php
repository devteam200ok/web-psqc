<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Certificate;
use App\Models\WebTest;
use App\Models\PsqcCertification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class HomePsqcCertified extends Component
{
    public $currentTest;
    public $code;
    public $certification;
    public $test_type = 'psqc';
    
    // Test type label mapping
    public $testTypeLabels = [
        // Performance
        'p-speed' => '[Performance] Global Speed',
        'p-load' => '[Performance] Load Test',
        'p-mobile' => '[Performance] Mobile Performance',
        
        // Security
        's-ssl' => '[Security] SSL Basic',
        's-sslyze' => '[Security] SSL Advanced',
        's-header' => '[Security] Security Headers',
        's-scan' => '[Security] Vulnerability Scan',
        's-nuclei' => '[Security] Latest Vulnerabilities',
        
        // Quality
        'q-lighthouse' => '[Quality] Overall Quality',
        'q-accessibility' => '[Quality] Accessibility (Advanced)',
        'q-compatibility' => '[Quality] Browser Compatibility',
        'q-visual' => '[Quality] Responsive UI',
        
        // Content
        'c-links' => '[Content] Link Validation',
        'c-structure' => '[Content] Structured Data',
        'c-crawl' => '[Content] Site Crawling',
        'c-meta' => '[Content] Metadata',
    ];
    
    public $testTypes = [
        'p-speed',
        'p-load',
        'p-mobile',
        's-ssl',
        's-sslyze',
        's-header',
        's-scan',
        's-nuclei',
        'q-lighthouse',
        'q-accessibility',
        'q-compatibility',
        'q-visual',
        'c-links',
        'c-structure',
        'c-crawl',
        'c-meta',
    ];
    
    public $mainTabActive = 'results';

    public function mount()
    {
        $this->certification = PsqcCertification::where('code', $this->code)->first();
        if (!$this->certification) {
            return redirect()->route('home');
        }
        if($this->certification->payment_status != 'paid'){
            return redirect()->route('home');
        }
    }

    public function generateCertificatePdf()
    {
        \Illuminate\Support\Facades\Artisan::call('make-psqc-pdf', [
            'code'    => $this->certification->code,
            '--force' => true,
        ]);

        $rel = "psqc-certificates/{$this->certification->code}.pdf";
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($rel)) {
            session()->flash('success', 'Certificate PDF has been generated.');
        } else {
            session()->flash('error', 'Failed to generate PDF.');
        }
    }

    public function render()
    {
        if($this->test_type != 'psqc'){
            $this->currentTest = WebTest::where('psqc_certification_id', $this->certification->id)
                ->where('test_type', $this->test_type)
                ->first();
            if(!$this->currentTest){
                $this->test_type = 'psqc';
                session()->flash('error', 'No detailed data for the selected test. Showing PSQC overall results.');
            }
        }

        return view('livewire.home-psqc-certified')
            ->layout('layouts.auth');
    }
}