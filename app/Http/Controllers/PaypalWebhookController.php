<?php

// app/Http/Controllers/PaypalWebhookController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\UserPlan;
use App\Models\Api;
use Carbon\Carbon;

class PaypalWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // PayPal에서 보낸 웹훅 데이터
        $webhookData = $request->all();
        $eventType = $webhookData['event_type'] ?? null;

        Log::info('PayPal Webhook received', [
            'event_type' => $eventType,
            'data' => $webhookData
        ]);

        // 웹훅 서명 검증 (프로덕션에서는 필수)
        if (!$this->verifyWebhookSignature($request)) {
            Log::error('PayPal webhook signature verification failed');
            return response('Unauthorized', 401);
        }

        try {
            switch ($eventType) {
                case 'BILLING.SUBSCRIPTION.ACTIVATED':
                    $this->handleSubscriptionActivated($webhookData);
                    break;
                    
                case 'PAYMENT.SALE.COMPLETED':
                    $this->handlePaymentCompleted($webhookData);
                    break;
                    
                case 'BILLING.SUBSCRIPTION.CANCELLED':
                    $this->handleSubscriptionCancelled($webhookData);
                    break;
                    
                case 'BILLING.SUBSCRIPTION.SUSPENDED':
                    $this->handleSubscriptionSuspended($webhookData);
                    break;
                    
                case 'BILLING.SUBSCRIPTION.PAYMENT.FAILED':
                    $this->handlePaymentFailed($webhookData);
                    break;
                    
                default:
                    Log::info('Unhandled PayPal webhook event', ['event_type' => $eventType]);
            }

            return response('OK', 200);
            
        } catch (\Exception $e) {
            Log::error('PayPal webhook processing error', [
                'error' => $e->getMessage(),
                'event_type' => $eventType,
                'data' => $webhookData
            ]);
            
            return response('Error', 500);
        }
    }

    private function verifyWebhookSignature(Request $request)
    {
        // 실제 프로덕션에서는 PayPal 웹훅 서명을 검증해야 합니다.
        // 지금은 개발 목적으로 true를 반환합니다.
        return true;
        
        /*
        // 실제 검증 로직 예제:
        $api = Api::first();
        $webhookId = $api->paypal_webhook_id; // PayPal에서 제공하는 Webhook ID
        
        $headers = $request->headers->all();
        $body = $request->getContent();
        
        // PayPal SDK를 사용한 서명 검증
        // return PayPal::verifyWebhookSignature($headers, $body, $webhookId);
        */
    }

    private function handleSubscriptionActivated($data)
    {
        $subscriptionId = $data['resource']['id'] ?? null;
        
        if (!$subscriptionId) {
            Log::error('Subscription ID not found in webhook data');
            return;
        }

        $userPlan = UserPlan::where('paypal_subscription_id', $subscriptionId)->first();
        
        if ($userPlan) {
            $userPlan->update([
                'status' => 'active',
                'payment_status' => 'paid'
            ]);
            
            Log::info('Subscription activated', ['subscription_id' => $subscriptionId]);
        }
    }

    private function handlePaymentCompleted($data)
    {
        $billingAgreementId = $data['resource']['billing_agreement_id'] ?? null;
        $amount = $data['resource']['amount']['total'] ?? null;
        
        if (!$billingAgreementId) {
            Log::error('Billing agreement ID not found in payment webhook');
            return;
        }

        $userPlan = UserPlan::where('paypal_subscription_id', $billingAgreementId)->first();
        
        if ($userPlan && $userPlan->is_subscription) {
            // 구독 갱신 - 다음 결제일까지 연장
            $currentEndDate = $userPlan->end_date;
            $newEndDate = $currentEndDate->addMonth();
            
            $userPlan->update([
                'end_date' => $newEndDate,
                'paypal_paid_at' => now(),
                'status' => 'active',
                'payment_status' => 'paid'
            ]);
            
            // 월간 사용량 리셋
            $userPlan->resetMonthlyUsage();
            
            Log::info('Subscription payment completed and renewed', [
                'subscription_id' => $billingAgreementId,
                'amount' => $amount,
                'new_end_date' => $newEndDate
            ]);
        }
    }

    private function handleSubscriptionCancelled($data)
    {
        $subscriptionId = $data['resource']['id'] ?? null;
        
        if (!$subscriptionId) {
            Log::error('Subscription ID not found in cancellation webhook');
            return;
        }

        $userPlan = UserPlan::where('paypal_subscription_id', $subscriptionId)->first();
        
        if ($userPlan) {
            $userPlan->update([
                'status' => 'cancelled',
                'auto_renew' => false
            ]);
            
            Log::info('Subscription cancelled', ['subscription_id' => $subscriptionId]);
        }
    }

    private function handleSubscriptionSuspended($data)
    {
        $subscriptionId = $data['resource']['id'] ?? null;
        
        if (!$subscriptionId) {
            Log::error('Subscription ID not found in suspension webhook');
            return;
        }

        $userPlan = UserPlan::where('paypal_subscription_id', $subscriptionId)->first();
        
        if ($userPlan) {
            $userPlan->update([
                'status' => 'suspended'
            ]);
            
            Log::info('Subscription suspended', ['subscription_id' => $subscriptionId]);
        }
    }

    private function handlePaymentFailed($data)
    {
        $subscriptionId = $data['resource']['id'] ?? null;
        
        if (!$subscriptionId) {
            Log::error('Subscription ID not found in payment failed webhook');
            return;
        }

        $userPlan = UserPlan::where('paypal_subscription_id', $subscriptionId)->first();
        
        if ($userPlan) {
            $userPlan->update([
                'payment_status' => 'failed'
            ]);
            
            Log::warning('Subscription payment failed', ['subscription_id' => $subscriptionId]);
            
            // 여기서 사용자에게 알림 이메일을 보낼 수 있습니다.
        }
    }
}