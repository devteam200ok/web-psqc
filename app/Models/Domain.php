<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Services\DomainVerificationService;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'url',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    // Accessors
    public function getDisplayNameAttribute(): string
    {
        $parsed = parse_url($this->url);
        $host = $parsed['host'] ?? $this->url;
        
        // Remove www. (optional)
        if (str_starts_with($host, 'www.')) {
            $host = substr($host, 4);
        }
        
        return $host;
    }

    public function getDomainOnlyAttribute(): string
    {
        $parsed = parse_url($this->url);
        return $parsed['host'] ?? $this->url;
    }

    public function getVerificationStatusAttribute(): string
    {
        if ($this->is_verified) {
            return 'Verified';
        }
        
        return $this->verification_token ? 'Pending' : 'Unverified';
    }

    public function getVerificationStatusClassAttribute(): string
    {
        if ($this->is_verified) {
            return 'badge bg-success';
        }
        
        return $this->verification_token ? 'badge bg-warning' : 'badge bg-secondary';
    }

    // Generate TXT record value for domain verification
    public function getTxtRecordValueAttribute(): string
    {
        return "devteam-verification={$this->verification_token}";
    }

    // Generate filename for file-based verification
    public function getVerificationFileNameAttribute(): string
    {
        return "devteam-verification-{$this->verification_token}.txt";
    }

    // Generate file content for file-based verification
    public function getVerificationFileContentAttribute(): string
    {
        return "DevTeam Domain Verification\nToken: {$this->verification_token}\nDomain: {$this->domain_only}\nUser ID: {$this->user_id}\nGenerated: " . $this->created_at->toISOString();
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($domain) {
            $domain->verification_token = Str::random(32);
        });
        
        // Run automatic verification check after creation
        static::created(function ($domain) {
            DomainVerificationService::checkExistingVerification($domain);
        });
        
        static::updating(function ($domain) {
            if ($domain->isDirty('url')) {
                if ($domain->is_verified) {
                    $domain->is_verified = false;
                    $domain->verified_at = null;
                    $domain->verification_token = Str::random(32);
                }
            }
        });
    }

    // Normalize URL and generate hash
    public function setUrlAttribute($value)
    {
        try {
            // Prepend https:// if missing
            if (!str_starts_with($value, 'http://') && !str_starts_with($value, 'https://')) {
                $value = 'https://' . $value;
            }
            
            $this->attributes['url'] = $value;
            $this->attributes['url_hash'] = hash('sha256', $value);
            
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Domain verification methods
    public function generateNewVerificationToken(): void
    {
        $this->verification_token = Str::random(32);
        $this->is_verified = false;
        $this->verified_at = null;
        $this->save();
    }

    public function markAsVerified(string $method = 'manual'): void
    {
        $this->is_verified = true;
        $this->verified_at = now();
        $this->verification_method = $method;
        $this->save();
        
        // Automatically verify related domains
        DomainVerificationService::verifyRelatedDomains($this);
    }

    public function resetVerification(): void
    {
        $this->is_verified = false;
        $this->verified_at = null;
        $this->verification_method = null;
        $this->save();
    }
}