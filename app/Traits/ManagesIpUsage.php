<?php
namespace App\Traits;

use App\Models\IpUsage;
use Illuminate\Support\Facades\Auth;

trait ManagesIpUsage
{
    public $ipUsage;
    public $ipAddress;

    public function initializeManagesIpUsage()
    {
        if (!Auth::check()) {
            $this->ipAddress = request()->ip();
            $this->ipUsage = IpUsage::initializeIp($this->ipAddress);
        }
    }

    public function consumeUsage()
    {
        if (!Auth::check() && $this->ipUsage) {
            return $this->ipUsage->decrementUsage();
        }
        return true; // 인증된 사용자는 항상 허용
    }

    public function hasUsageRemaining()
    {
        if (!Auth::check() && $this->ipUsage) {
            return $this->ipUsage->hasUsage();
        }
        return true; // 인증된 사용자는 항상 허용
    }
}