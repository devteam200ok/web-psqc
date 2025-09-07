<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ScheduledTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'test_type',
        'url',
        'domain',
        'scheduled_at',
        'status',
        'test_config',
        'executed_at',
        'executed_test_id', // WebTest ID after execution
        'error_message',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'executed_at' => 'datetime',
        'test_config' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function executedTest(): BelongsTo
    {
        return $this->belongsTo(WebTest::class, 'executed_test_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExecuted($query)
    {
        return $query->where('status', 'executed');
    }

    public function scopeDue($query)
    {
        return $query->where('scheduled_at', '<=', now());
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByTestType($query, string $testType)
    {
        return $query->where('test_type', $testType);
    }

    // Accessors
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'badge bg-orange-lt text-orange-lt-fg',   // Pending - Orange
            'executed'  => 'badge bg-green-lt text-green-lt-fg',     // Executed - Green
            'failed'    => 'badge bg-red-lt text-red-lt-fg',         // Failed - Red
            'cancelled' => 'badge bg-azure-lt text-azure-lt-fg',     // Cancelled - Gray/Blue
            default     => 'badge bg-blue-lt text-blue-lt-fg'        // Default - Blue
        };
    }

    public function getTestTypeNameAttribute(): string
    {
        $testTypes = WebTest::getTestTypes();
        return $testTypes[$this->test_type] ?? $this->test_type;
    }

    public function getShortDomainAttribute(): string
    {
        return str_replace(['https://', 'http://', 'www.'], '', $this->domain);
    }

    public function getTimeUntilExecutionAttribute(): string
    {
        if ($this->status !== 'pending') {
            return '';
        }

        $now = now();
        if ($this->scheduled_at <= $now) {
            return 'Waiting to be executed';
        }

        return $this->scheduled_at->diffForHumans($now);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'pending' && $this->scheduled_at < now()->subMinutes(5);
    }

    // Mutators
    public function setUrlAttribute($value)
    {
        $this->attributes['url'] = $value;
        $this->attributes['domain'] = $this->extractDomain($value);
    }

    // Helper methods
    private function extractDomain(string $url): string
    {
        $parsed = parse_url($url);
        return $parsed['host'] ?? $url;
    }

    public function canBeCancelled(): bool
    {
        return $this->status === 'pending' && $this->scheduled_at > now();
    }

    public function cancel(): void
    {
        if ($this->canBeCancelled()) {
            $this->update(['status' => 'cancelled']);
        }
    }

    public function markAsExecuted(int $webTestId): void
    {
        $this->update([
            'status' => 'executed',
            'executed_at' => now(),
            'executed_test_id' => $webTestId
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'executed_at' => now(),
            'error_message' => $errorMessage
        ]);
    }

    // Static methods
    public static function getStatuses(): array
    {
        return [
            'pending'   => 'Pending',
            'executed'  => 'Executed',
            'failed'    => 'Failed',
            'cancelled' => 'Cancelled'
        ];
    }

    public static function cleanupOldSchedules(int $daysToKeep = 30): int
    {
        return static::whereIn('status', ['executed', 'failed', 'cancelled'])
            ->where('created_at', '<', now()->subDays($daysToKeep))
            ->delete();
    }
}