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
                // 구독 관련 이벤트
                case 'BILLING.SUBSCRIPTION.ACTIVATED':
                    $this->handleSubscriptionActivated($webhookData);
                    break;
                    
                case 'BILLING.SUBSCRIPTION.CANCELLED':
                    $this->handleSubscriptionCancelled($webhookData);
                    break;
                    
                case 'BILLING.SUBSCRIPTION.SUSPENDED':
                    $this->handleSubscriptionSuspended($webhookData);
                    break;
                    
                case 'BILLING.SUBSCRIPTION.EXPIRED':
                    $this->handleSubscriptionExpired($webhookData);
                    break;
                    
                case 'BILLING.SUBSCRIPTION.PAYMENT.SUCCESS':
                    $this->handleSubscriptionPaymentSuccess($webhookData);
                    break;
                    
                case 'BILLING.SUBSCRIPTION.PAYMENT.FAILED':
                    $this->handleSubscriptionPaymentFailed($webhookData);
                    break;
                
                // 일회성 결제 관련 이벤트
                case 'PAYMENT.CAPTURE.COMPLETED':
                    $this->handlePaymentCaptureCompleted($webhookData);
                    break;
                    
                case 'PAYMENT.CAPTURE.DENIED':
                    $this->handlePaymentCaptureDenied($webhookData);
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

    // 구독이 활성화되었을 때
    private function handleSubscriptionActivated($data)
    {
        $subscriptionId = $data['resource']['id'] ?? null;
        
        if (!$subscriptionId) {
            Log::error('Subscription ID not found in activation webhook');
            return;
        }

        $userPlan = UserPlan::where('paypal_subscription_id', $subscriptionId)->first();
        
        if ($userPlan) {
            $userPlan->update([
                'status' => 'active',
                'payment_status' => 'paid'
            ]);
            
            Log::info('Subscription activated via webhook', ['subscription_id' => $subscriptionId]);
        } else {
            Log::warning('UserPlan not found for subscription activation', ['subscription_id' => $subscriptionId]);
        }
    }

    // 구독 결제 성공 (월간 갱신)
    private function handleSubscriptionPaymentSuccess($data)
    {
        $subscriptionId = $data['resource']['billing_agreement_id'] ?? $data['resource']['id'] ?? null;
        $amount = $data['resource']['amount']['value'] ?? $data['resource']['amount']['total'] ?? null;
        
        if (!$subscriptionId) {
            Log::error('Subscription ID not found in payment success webhook');
            return;
        }

        $userPlan = UserPlan::where('paypal_subscription_id', $subscriptionId)->first();
        
        if ($userPlan && $userPlan->is_subscription) {
            // 구독 갱신 - 다음 결제일까지 연장
            $currentEndDate = $userPlan->end_date;
            $newEndDate = $currentEndDate->addMonth();
            
            $userPlan->update([
                'end_date' => $newEndDate,
                'paypal_paid_at' => now(),
                'status' => 'active',
                'payment_status' => 'paid',
                'payment_failure_count' => 0, // 성공 시 실패 카운트 리셋
            ]);
            
            // 월간 사용량 리셋
            if (method_exists($userPlan, 'resetMonthlyUsage')) {
                $userPlan->resetMonthlyUsage();
            }
            
            Log::info('Subscription payment success and renewed', [
                'subscription_id' => $subscriptionId,
                'amount' => $amount,
                'new_end_date' => $newEndDate
            ]);
        } else {
            Log::warning('UserPlan not found for subscription payment success', ['subscription_id' => $subscriptionId]);
        }
    }

    // 구독 결제 실패
    private function handleSubscriptionPaymentFailed($data)
    {
        $subscriptionId = $data['resource']['id'] ?? null;
        
        if (!$subscriptionId) {
            Log::error('Subscription ID not found in payment failed webhook');
            return;
        }

        $userPlan = UserPlan::where('paypal_subscription_id', $subscriptionId)->first();
        
        if ($userPlan) {
            // 결제 실패 카운트 증가
            if (method_exists($userPlan, 'incrementPaymentFailure')) {
                $userPlan->incrementPaymentFailure();
            } else {
                $userPlan->update([
                    'payment_status' => 'failed',
                    'payment_failure_count' => ($userPlan->payment_failure_count ?? 0) + 1
                ]);
                
                // 3회 실패 시 구독 중단
                if (($userPlan->payment_failure_count ?? 0) >= 3) {
                    $userPlan->update([
                        'status' => 'suspended',
                        'auto_renew' => false
                    ]);
                }
            }
            
            Log::warning('Subscription payment failed', [
                'subscription_id' => $subscriptionId,
                'failure_count' => $userPlan->payment_failure_count ?? 0
            ]);
            
            // 여기서 사용자에게 결제 실패 알림 이메일을 보낼 수 있습니다.
        } else {
            Log::warning('UserPlan not found for subscription payment failure', ['subscription_id' => $subscriptionId]);
        }
    }

    // 구독 취소
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
            
            Log::info('Subscription cancelled via webhook', ['subscription_id' => $subscriptionId]);
        } else {
            Log::warning('UserPlan not found for subscription cancellation', ['subscription_id' => $subscriptionId]);
        }
    }

    // 구독 중단 (결제 문제로 인한)
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
                'status' => 'suspended',
                'auto_renew' => false
            ]);
            
            Log::info('Subscription suspended via webhook', ['subscription_id' => $subscriptionId]);
        } else {
            Log::warning('UserPlan not found for subscription suspension', ['subscription_id' => $subscriptionId]);
        }
    }

    // 구독 만료
    private function handleSubscriptionExpired($data)
    {
        $subscriptionId = $data['resource']['id'] ?? null;
        
        if (!$subscriptionId) {
            Log::error('Subscription ID not found in expiration webhook');
            return;
        }

        $userPlan = UserPlan::where('paypal_subscription_id', $subscriptionId)->first();
        
        if ($userPlan) {
            $userPlan->update([
                'status' => 'expired',
                'auto_renew' => false
            ]);
            
            Log::info('Subscription expired via webhook', ['subscription_id' => $subscriptionId]);
        } else {
            Log::warning('UserPlan not found for subscription expiration', ['subscription_id' => $subscriptionId]);
        }
    }

    // 일회성 결제 완료 (쿠폰)
    private function handlePaymentCaptureCompleted($data)
    {
        $orderId = $data['resource']['supplementary_data']['related_ids']['order_id'] ?? null;
        $captureId = $data['resource']['id'] ?? null;
        $amount = $data['resource']['amount']['value'] ?? null;
        
        if (!$orderId && !$captureId) {
            Log::error('Order ID or Capture ID not found in payment capture webhook');
            return;
        }

        // Order ID 또는 Capture ID로 UserPlan 찾기
        $userPlan = UserPlan::where('paypal_order_id', $orderId)
            ->orWhere('paypal_order_id', $captureId)
            ->first();
        
        if ($userPlan && !$userPlan->is_subscription) {
            $userPlan->update([
                'status' => 'active',
                'payment_status' => 'paid',
                'paypal_paid_at' => now()
            ]);
            
            Log::info('One-time payment captured successfully', [
                'order_id' => $orderId,
                'capture_id' => $captureId,
                'amount' => $amount
            ]);
        } else {
            Log::warning('UserPlan not found for payment capture', [
                'order_id' => $orderId,
                'capture_id' => $captureId
            ]);
        }
    }

    // 일회성 결제 거부
    private function handlePaymentCaptureDenied($data)
    {
        $orderId = $data['resource']['supplementary_data']['related_ids']['order_id'] ?? null;
        $captureId = $data['resource']['id'] ?? null;
        
        if (!$orderId && !$captureId) {
            Log::error('Order ID or Capture ID not found in payment denied webhook');
            return;
        }

        $userPlan = UserPlan::where('paypal_order_id', $orderId)
            ->orWhere('paypal_order_id', $captureId)
            ->first();
        
        if ($userPlan) {
            $userPlan->update([
                'status' => 'cancelled',
                'payment_status' => 'failed'
            ]);
            
            Log::warning('One-time payment denied', [
                'order_id' => $orderId,
                'capture_id' => $captureId
            ]);
        } else {
            Log::warning('UserPlan not found for payment denial', [
                'order_id' => $orderId,
                'capture_id' => $captureId
            ]);
        }
    }
}