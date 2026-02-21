<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $fillable = [
        'user_id',
        'agent_id',
        'public_id',
        'title',
        'description',
        'type',
        'category',
        'price',
        'currency',
        'location',
        'city',
        'country',
        'latitude',
        'longitude',
        'images',
        'video_path',
        'bedrooms',
        'bathrooms',
        'area',
        'cuisine',
        'amenities',
        'year_built',
        'previous_renters',
        'condition',
        'is_featured',
        'views',
        'status',
    ];

    protected $casts = [
        'images' => 'array',
        'amenities' => 'array',
        'is_featured' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->public_id)) {
                $model->public_id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
