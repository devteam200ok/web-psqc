<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'email_verified_at',
        'name',
        'password',
        'role',
        'verification_code',
        'usage',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    public function verifiedDomains()
    {
        return $this->hasMany(Domain::class)->where('is_verified', true);
    }
    
    public function scheduledTests()
    {
        return $this->hasMany(ScheduledTest::class);
    }

    public function pendingScheduledTests()
    {
        return $this->hasMany(ScheduledTest::class)->where('status', 'pending');
    }
}
