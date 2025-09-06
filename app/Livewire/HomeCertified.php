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
            session()->flash('success', '인증서 PDF가 생성되었습니다.');
        } else {
            session()->flash('error', 'PDF 생성에 실패했습니다.');
        }
    }

    public function render()
    {
        return view('livewire.home-certified')
            ->layout('layouts.auth');
    }
}