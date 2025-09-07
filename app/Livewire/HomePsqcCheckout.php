<?php

namespace App\Livewire;

use App\Models\PsqcCertification;
use App\Models\Api;
use App\Models\WebTest;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class HomePsqcCheckout extends Component
{
    public PsqcCertification $certification;
    public $amount = 59; // Certification issuance fee (USD)
    public $orderId;
    public $testDetails = [];

    // PayPal 관련 속성
    public $paypal_mode;
    public $paypal_client_id;

    protected $listeners = [
        'paypal-psqc-payment-verified' => 'verifyPaypalPayment',
    ];

    public function mount($certificate)
    {
        if (!Auth::check()) {
            abort(401, 'Login is required.');
        }

        // Find certification directly by ID
        $this->certification = PsqcCertification::find($certificate);
        
        if (!$this->certification) {
            abort(404, 'Certification not found.');
        }

        if ($this->certification->user_id !== Auth::id()) {
            abort(403, 'You do not have permission.');
        }

        // Check if certification is still pending payment
        if ($this->certification->payment_status !== 'pending') {
            return redirect()->route('home')->with('error', 'This certification has already been processed.');
        }

        // PayPal 설정 로드
        $api = Api::first();
        $this->paypal_mode = $api->paypal_mode;

        if ($this->paypal_mode == 'live') {
            $this->paypal_client_id = $api->paypal_client_id_live;
        } else {
            $this->paypal_client_id = $api->paypal_client_id_sandbox;
        }
        
        // Generate order ID (based on certification ID)
        $this->orderId = 'PSQC_' . $this->certification->id . '_' . time();
        
        // Build test details
        $this->buildTestDetails();
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
            return redirect()->route('psqc.checkout', ['certificate' => $this->certification->id])
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
            return redirect()->route('psqc.checkout', ['certificate' => $this->certification->id])
                ->with('error', 'Payment verification failed. Please try again.');
        }

        $data = $orderResponse->json();

        // 검증 1: 상태 확인
        if ($data['status'] !== 'COMPLETED') {
            Log::error('PayPal payment not completed', $data);
            return redirect()->route('psqc.checkout', ['certificate' => $this->certification->id])
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
            return redirect()->route('psqc.checkout', ['certificate' => $this->certification->id])
                ->with('error', 'Payment amount verification failed. Please try again.');
        }

        // 결제 성공 - 인증서 상태 업데이트
        $this->certification->markAsPaid([
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

        // PSQC 인증서 PDF 생성
        $this->generatePsqcPdf($this->certification->code);

        return redirect()->route('client.psqc')
            ->with('success', 'Payment completed successfully. Your certification has been issued.');
    }

    private function generatePsqcPdf($code)
    {
        Artisan::call('cert:make-psqc-pdf', [
            'code' => $code,
            '--force' => true,
        ]);
    }

    private function buildTestDetails()
    {
        $testTypes = WebTest::getTestTypes();
        $metrics = $this->certification->metrics;
        
        $groups = [
            'Performance (P)' => ['p-speed', 'p-load', 'p-mobile'],
            'Security (S)' => ['s-ssl', 's-sslyze', 's-header', 's-scan', 's-nuclei'],
            'Quality (Q)' => ['q-lighthouse', 'q-accessibility', 'q-compatibility', 'q-visual'],
            'Content (C)' => ['c-links', 'c-structure', 'c-crawl', 'c-meta'],
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
            str_contains($groupLabel, 'Performance') => 'performance',
            str_contains($groupLabel, 'Security') => 'security',
            str_contains($groupLabel, 'Quality') => 'quality',
            str_contains($groupLabel, 'Content') => 'content',
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