<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\WebTest;
use App\Models\Api;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function paymentSuccess(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('home')->with('error', '로그인이 필요합니다.');
        }

        $paymentType = $request->get('paymentType');
        $orderId = $request->get('orderId');
        $paymentKey = $request->get('paymentKey');
        $amount = $request->get('amount');
        $certificateId = $request->get('certificate_id');

        $certificate = Certificate::where('id', $certificateId)
            ->where('user_id', Auth::id())
            ->where('payment_status', 'pending')
            ->first();

        if (!$certificate) {
            return redirect()->route('home')->with('error', '인증서를 찾을 수 없습니다.');
        }

        $api = Api::first();
        $toss_secret_key = $api->toss_mode == 'live' 
            ? $api->toss_secret_key 
            : $api->toss_secret_key_test;
        $toss_secret_key = base64_encode($toss_secret_key . ':');

        $apiURL = 'https://api.tosspayments.com/v1/payments/confirm';
        $headers = [
            'Authorization' => 'Basic ' . $toss_secret_key,
            'Content-Type' => 'application/json',
        ];

        $postInput = [
            'paymentKey' => $paymentKey,
            'orderId' => $orderId,
            'amount' => $amount
        ];

        $response = Http::withHeaders($headers)->post($apiURL, $postInput);
        $statusCode = $response->status();

        if ($statusCode == 200) {
            // 결제 성공 - 인증서 상태 업데이트
            $certificate->markAsPaid([
                'payment_type' => $paymentType,
                'payment_key' => $paymentKey,
                'order_id' => $orderId,
                'amount' => $amount,
                'toss_response' => $response->json()
            ]);

            $webTest = WebTest::find($certificate->web_test_id);
            if ($webTest) {
                $webTest->is_saved_permanently = true;
                $webTest->save();
            }

            // 인증서 PDF 생성
            $this->generateCertificatePdf($certificate->code);

            return redirect()->route('client.certificate')
                ->with('success', '결제가 완료되었습니다. 인증서가 발급되었습니다.');
        } else {
            // 결제 실패
            $certificate->markAsFailed([
                'error_response' => $response->json(),
                'status_code' => $statusCode
            ]);

            return redirect()->route('certificate.checkout', ['certificate' => $certificate->id])
                ->with('error', '결제에 실패했습니다. 다시 시도해주세요.');
        }
    }

    public function paymentFail(Request $request)
    {
        $certificateId = $request->get('certificate_id');
        
        if ($certificateId && Auth::check()) {
            $certificate = Certificate::where('id', $certificateId)
                ->where('user_id', Auth::id())
                ->first();
                
            if ($certificate) {
                $certificate->markAsFailed(['reason' => 'user_cancelled']);
            }
        }

        return redirect()->route('home')->with('error', '결제가 취소되었습니다.');
    }

    public function generateCertificatePdf($code)
    {
        \Illuminate\Support\Facades\Artisan::call('cert:make-pdf', [
            'code'    => $code,
            '--force' => true,
        ]);
    }
}