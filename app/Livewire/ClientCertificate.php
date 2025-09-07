<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Certificate;
use App\Models\WebTest;

class ClientCertificate extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $dateFrom = '';
    public $dateTo = '';
    public $status = 'all'; // all|valid|expired
    public $type = 'all';   // all or test_type key
    public $perPage = 12;

    public function updated($name)
    {
        // Any filter change resets pagination
        if (in_array($name, ['dateFrom', 'dateTo', 'status', 'type'])) {
            $this->resetPage();
        }
    }

    public function clearFilters()
    {
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->status = 'all';
        $this->type = 'all';
        $this->resetPage();
    }

    public function render()
    {
        $query = Certificate::query()
            ->where('user_id', auth()->id())
            ->where('payment_status', 'paid'); // Only issued certificates

        if (!empty($this->dateFrom)) {
            $query->whereDate('issued_at', '>=', $this->dateFrom);
        }
        if (!empty($this->dateTo)) {
            $query->whereDate('issued_at', '<=', $this->dateTo);
        }

        if ($this->status === 'valid') {
            $query->where('is_valid', true)
                  ->where(function ($q) {
                      $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                  });
        } elseif ($this->status === 'expired') {
            $query->where(function ($q) {
                $q->where('is_valid', false)
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('expires_at')->where('expires_at', '<=', now());
                  });
            });
        }

        if ($this->type !== 'all') {
            $query->where('test_type', $this->type);
        }

        $certificates = $query->orderByDesc('issued_at')
                              ->orderByDesc('id')
                              ->paginate($this->perPage);

        $testTypes = WebTest::getTestTypes();

        return view('livewire.client-certificate', [
            'certificates' => $certificates,
            'testTypes' => $testTypes,
        ])->layout('layouts.app');
    }
}
