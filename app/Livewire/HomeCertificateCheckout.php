<?php

namespace App\Livewire;

use App\Models\Certificate;
use App\Models\Api;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class HomeCertificateCheckout extends Component
{
    public Certificate $certificate;
    public $amount = 19; // Certification issuance fee (USD)
    public $orderId;

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
        
        // Generate order ID (based on certificate ID)
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