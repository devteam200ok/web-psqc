<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Certificate;
use App\Models\WebTest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class HomeCertified extends Component
{
    public $code;
    public $certificate;
    public $currentTest;
    public $test_type;
    public $mainTabActive = 'results';

    public function mount()
    {
        $this->certificate = Certificate::where('code', $this->code)->first();
        if (!$this->certificate) {
            return redirect()->route('home');
        }
        if($this->certificate->payment_status != 'paid'){
            return redirect()->route('home');
        }
        $this->test_type   = $this->certificate->test_type;
        $this->currentTest = WebTest::find($this->certificate->web_test_id);
    }

    public function generateCertificatePdf()
    {
        \Illuminate\Support\Facades\Artisan::call('cert:make-pdf', [
            'code'    => $this->certificate->code,
            '--force' => true,
        ]);

        $rel = "certification/{$this->certificate->code}.pdf";
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($rel)) {
            session()->flash('success', 'Certificate PDF has been generated.');
        } else {
            session()->flash('error', 'Failed to generate PDF.');
        }
    }

    public function render()
    {
        return view('livewire.home-certified')
            ->layout('layouts.auth');
    }
}