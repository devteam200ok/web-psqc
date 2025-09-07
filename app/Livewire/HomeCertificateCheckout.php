<?php

namespace App\Livewire;

use App\Models\Certificate;
use App\Models\WebTest;
use App\Models\Api;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class HomeCertificateCheckout extends Component
{
    public Certificate $certificate;
    public $amount = 19; // Certificate issuance fee (USD)
    public $orderId;

    // PayPal 관련 속성
    public $paypal_mode;
    public $paypal_client_id;

    protected $listeners = [
        'paypal-certificate-payment-verified' => 'verifyPaypalPayment',
    ];

    public function mount(Certificate $certificate)
    {
        if (!Auth::check()) {
            abort(401, 'Login is required.');
        }

        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'You do not have permission.');
        }

        // Verify that payment is still pending
        if ($certificate->payment_status !== 'pending') {
            return redirect()->route('home')->with('error', 'This certificate has already been processed.');
        }

        $this->certificate = $certificate;
        
        // PayPal 설정 로드
        $api = Api::first();
        $this->paypal_mode = $api->paypal_mode;

        if ($this->paypal_mode == 'live') {
            $this->paypal_client_id = $api->paypal_client_id_live;
        } else {
            $this->paypal_client_id = $api->paypal_client_id_sandbox;
        }

        // Generate order ID (based on certificate ID)
        $this->orderId = 'CERT_' . $certificate->id . '_' . time();
    }

    private function getPaypalAccessToken()
    {
        $api = Api::first();
        
        $baseUrl = $this->paypal_mode == 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';

        // Secret은 서버에서만 사용
        $paypal_secret = $this->paypal_mode == 'live' 
            ? $api->paypal_secret_live 
            : $api->paypal_secret_sandbox;

        $tokenResponse = Http::asForm()
            ->withBasicAuth($this->paypal_client_id, $paypal_secret)
            ->post("{$baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if (!$tokenResponse->ok()) {
            Log::error('PayPal token request failed', $tokenResponse->json());
            return null;
        }

        return $tokenResponse->json('access_token');
    }

    public function verifyPaypalPayment($order_id)
    {
        $accessToken = $this->getPaypalAccessToken();
        if (!$accessToken) {
            return redirect()->route('certificate.checkout', ['certificate' => $this->certificate->id])
                ->with('error', 'Payment verification failed. Please try again.');
        }

        $baseUrl = $this->paypal_mode == 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';

        // PayPal Order Lookup
        $orderResponse = Http::withToken($accessToken)
            ->get("{$baseUrl}/v2/checkout/orders/{$order_id}");

        if (!$orderResponse->ok()) {
            Log::error('PayPal order lookup failed', $orderResponse->json());
            return redirect()->route('certificate.checkout', ['certificate' => $this->certificate->id])
                ->with('error', 'Payment verification failed. Please try again.');
        }

        $data = $orderResponse->json();

        // 검증 1: 상태 확인
        if ($data['status'] !== 'COMPLETED') {
            Log::error('PayPal payment not completed', $data);
            return redirect()->route('certificate.checkout', ['certificate' => $this->certificate->id])
                ->with('error', 'Payment was not completed. Please try again.');
        }

        // 검증 2: 금액/통화 확인
        $expectedAmount = number_format($this->amount, 2, '.', '');
        $actualAmount = $data['purchase_units'][0]['amount']['value'];
        $currency = $data['purchase_units'][0]['amount']['currency_code'];

        if ($actualAmount != $expectedAmount || $currency !== 'USD') {
            Log::error('PayPal payment amount mismatch', [
                'expected' => $expectedAmount,
                'actual' => $actualAmount,
                'currency' => $currency,
            ]);
            return redirect()->route('certificate.checkout', ['certificate' => $this->certificate->id])
                ->with('error', 'Payment amount verification failed. Please try again.');
        }

        // 결제 성공 - 인증서 상태 업데이트
        $this->certificate->markAsPaid([
            'payment_type' => 'paypal',
            'paypal_order_id' => $data['id'],
            'paypal_payer_id' => $data['payer']['payer_id'],
            'paypal_email' => $data['payer']['email_address'],
            'paypal_name' => $data['payer']['name']['given_name'] . ' ' . $data['payer']['name']['surname'],
            'paypal_amount' => $actualAmount,
            'paypal_currency' => $currency,
            'paypal_paid_at' => now(),
            'order_id' => $this->orderId,
            'amount' => $actualAmount,
            'paypal_response' => $data
        ]);

        // WebTest 영구 저장 설정
        $webTest = WebTest::find($this->certificate->web_test_id);
        if ($webTest) {
            $webTest->is_saved_permanently = true;
            $webTest->save();
        }

        // 인증서 PDF 생성
        $this->generateCertificatePdf($this->certificate->code);

        return redirect()->route('client.certificate')
            ->with('success', 'Payment completed successfully. Your certificate has been issued.');
    }

    private function generateCertificatePdf($code)
    {
        Artisan::call('cert:make-pdf', [
            'code' => $code,
            '--force' => true,
        ]);
    }

    public function render()
    {
        $api = Api::first();
        
        return view('livewire.home-certificate-checkout', [
            'api' => $api
        ])->layout('layouts.app');
    }
}