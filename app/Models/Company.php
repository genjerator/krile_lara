<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'category',
        'street',
        'postal_code',
        'city',
        'phone',
        'website',
        'email',
        'source_url',
        'scraped_at',
    ];

    protected $casts = [
        'scraped_at' => 'datetime',
    ];
}
