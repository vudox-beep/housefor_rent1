<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'country',
        'dob',
        'role',
        'status',
        'color',
        'subscription_plan',
        'subscription_expires_at',
        'trial_expires_at',
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
            'subscription_expires_at' => 'datetime',
            'trial_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDealer()
    {
        return $this->role === 'dealer';
    }

    public function isGold()
    {
        return $this->subscription_plan === 'gold' && 
               ($this->subscription_expires_at === null || $this->subscription_expires_at->isFuture());
    }

    public function hasActiveTrial(): bool
    {
        return $this->trial_expires_at !== null && $this->trial_expires_at->isFuture();
    }

    public function maxListingImages(): int
    {
        if ($this->isGold()) {
            return 5;
        }

        if ($this->hasActiveTrial()) {
            return 20;
        }

        return 1;
    }

    public function canUploadVideo(): bool
    {
        return $this->isGold() || $this->hasActiveTrial();
    }
}
