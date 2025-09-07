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
    
    // 테스트 타입 매핑 추가
    public $testTypeLabels = [
        // 성능(Performance)
        'p-speed' => '[성능] 글로벌 속도',
        'p-load' => '[성능] 부하 테스트',
        'p-mobile' => '[성능] 모바일 성능',
        
        // 보안(Security)
        's-ssl' => '[보안] SSL 기본',
        's-sslyze' => '[보안] SSL 심화',
        's-header' => '[보안] 보안 헤더',
        's-scan' => '[보안] 취약점 스캔',
        's-nuclei' => '[보안] 최신 취약점',
        
        // 품질(Quality)
        'q-lighthouse' => '[품질] 종합 품질',
        'q-accessibility' => '[품질] 접근성 심화',
        'q-compatibility' => '[품질] 브라우저 호환',
        'q-visual' => '[품질] 반응형 UI',
        
        // 콘텐츠(Content)
        'c-links' => '[콘텐츠] 링크 검증',
        'c-structure' => '[콘텐츠] 구조화 데이터',
        'c-crawl' => '[콘텐츠] 사이트 크롤링',
        'c-meta' => '[콘텐츠] 메타데이터',
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
            session()->flash('success', '인증서 PDF가 생성되었습니다.');
        } else {
            session()->flash('error', 'PDF 생성에 실패했습니다.');
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
                session()->flash('error', '선택한 테스트의 세부 데이터가 없습니다. PSQC 종합 결과로 이동합니다.');
            }
        }

        return view('livewire.home-psqc-certified')
            ->layout('layouts.auth');
    }
}