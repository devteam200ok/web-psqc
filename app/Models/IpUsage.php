<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpUsage extends Model
{
    protected $fillable = ['ip_address', 'usage'];

    public static function initializeIp($ipAddress)
    {
        return static::firstOrCreate(
            ['ip_address' => $ipAddress],
            ['usage' => 5]
        );
    }

    public function decrementUsage()
    {
        if ($this->usage > 0) {
            $this->decrement('usage');
            return true;
        }
        return false;
    }

    public function hasUsage()
    {
        return $this->usage > 0;
    }
}