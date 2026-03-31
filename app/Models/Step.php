<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Step extends Model
{
    use HasFactory;

    // Use the boolean false, not the string 'false'
    protected $attributes = [
        'completed' => false,
    ];

    // This ensures Laravel treats 0/1 from the DB as true/false in PHP
    protected $casts = [
        'completed' => 'boolean',
    ];

    public function idea(): BelongsTo
    {
        return $this->belongsTo(Idea::class);
    }
}
