<?php

namespace App\Livewire;

use App\Models\Certificate;
use App\Models\Api;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class HomeCertificateCheckout extends Component
{
    public Certificate $certificate;
    public $amount = 19000; // 인증서 발급 비용 (원)
    public $orderId;

    public function mount(Certificate $certificate)
    {
        if (!Auth::check()) {
            abort(401, '로그인이 필요합니다.');
        }

        if ($certificate->user_id !== Auth::id()) {
            abort(403, '권한이 없습니다.');
        }

        // 결제 대기 상태인지 확인
        if ($certificate->payment_status !== 'pending') {
            return redirect()->route('home')->with('error', '이미 처리된 인증서입니다.');
        }

        $this->certificate = $certificate;
        
        // 주문 ID 생성 (인증서 ID 기반)
        $this->orderId = 'CERT_' . $certificate->id . '_' . time();
    }

    public function render()
    {
        $api = Api::first();
        
        return view('livewire.home-certificate-checkout', [
            'api' => $api
        ])->layout('layouts.app');
    }
}