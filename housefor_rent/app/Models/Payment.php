<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'public_id',
        'amount',
        'currency',
        'payment_method',
        'transaction_id',
        'status',
        'type',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate a temporary transaction_id if none provided
        static::creating(function ($model) {
            if (empty($model->public_id)) {
                $model->public_id = (string) Str::uuid();
            }
            if (! $model->transaction_id) {
                $model->transaction_id = 'temp_' . Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
