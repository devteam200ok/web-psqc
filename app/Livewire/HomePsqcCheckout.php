<?php

namespace App\Livewire;

use App\Models\PsqcCertification;
use App\Models\Api;
use App\Models\WebTest;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class HomePsqcCheckout extends Component
{
    public PsqcCertification $certification;
    public $amount = 59; // 인증서 발급 비용 (원)
    public $orderId;
    public $testDetails = [];

    public function mount($certificate)
    {
        if (!Auth::check()) {
            abort(401, '로그인이 필요합니다.');
        }

        // ID로 직접 인증서 찾기
        $this->certification = PsqcCertification::find($certificate);
        
        if (!$this->certification) {
            abort(404, '인증서를 찾을 수 없습니다.');
        }

        if ($this->certification->user_id !== Auth::id()) {
            abort(403, '권한이 없습니다.');
        }

        // 결제 대기 상태인지 확인
        if ($this->certification->payment_status !== 'pending') {
            return redirect()->route('home')->with('error', '이미 처리된 인증서입니다.');
        }
        
        // 주문 ID 생성 (인증서 ID 기반)
        $this->orderId = 'PSQC_' . $this->certification->id . '_' . time();
        
        // 테스트 상세 정보 구성
        $this->buildTestDetails();
    }

    private function buildTestDetails()
    {
        $testTypes = WebTest::getTestTypes();
        $metrics = $this->certification->metrics;
        
        $groups = [
            '성능 (P)' => ['p-speed', 'p-load', 'p-mobile'],
            '보안 (S)' => ['s-ssl', 's-sslyze', 's-header', 's-scan', 's-nuclei'],
            '품질 (Q)' => ['q-lighthouse', 'q-accessibility', 'q-compatibility', 'q-visual'],
            '콘텐츠 (C)' => ['c-links', 'c-structure', 'c-crawl', 'c-meta'],
        ];
        
        $this->testDetails = [];
        
        foreach ($groups as $groupLabel => $keys) {
            $groupTests = [];
            $category = $this->getCategoryFromGroupLabel($groupLabel);
            
            foreach ($keys as $key) {
                $label = $testTypes[$key] ?? $key;
                $testData = $metrics[$category][$key] ?? null;
                
                $groupTests[] = [
                    'key' => $key,
                    'label' => $label,
                    'score' => $testData['score'] ?? null,
                    'grade' => $testData['grade'] ?? null,
                    'grade_color' => $this->getGradeColor($testData['grade'] ?? null),
                ];
            }
            
            $this->testDetails[$groupLabel] = $groupTests;
        }
    }
    
    private function getCategoryFromGroupLabel(string $groupLabel): string
    {
        return match(true) {
            str_contains($groupLabel, '성능') => 'performance',
            str_contains($groupLabel, '보안') => 'security',
            str_contains($groupLabel, '품질') => 'quality',
            str_contains($groupLabel, '콘텐츠') => 'content',
            default => 'other'
        };
    }
    
    private function getGradeColor(?string $grade): string
    {
        if (!$grade) return 'bg-secondary';
        
        return match($grade) {
            'A+' => 'bg-green-lt text-green-lt-fg',
            'A' => 'bg-lime-lt text-lime-lt-fg',
            'B' => 'bg-blue-lt text-blue-lt-fg',
            'C' => 'bg-yellow-lt text-yellow-lt-fg',
            'D' => 'bg-orange-lt text-orange-lt-fg',
            'F' => 'bg-red-lt text-red-lt-fg',
            default => 'bg-azure-lt text-azure-lt-fg'
        };
    }

    public function render()
    {
        $api = Api::first();

        return view('livewire.home-psqc-checkout', [
            'api' => $api,
            'testDetails' => $this->testDetails
        ])->layout('layouts.app');
    }
}