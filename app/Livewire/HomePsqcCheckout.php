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
    public $amount = 59; // Certification issuance fee (USD)
    public $orderId;
    public $testDetails = [];

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
        
        // Generate order ID (based on certification ID)
        $this->orderId = 'PSQC_' . $this->certification->id . '_' . time();
        
        // Build test details
        $this->buildTestDetails();
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