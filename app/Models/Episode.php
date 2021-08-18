<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Episode extends Model
{
    use HasFactory;

    protected $withCount = ['comments'];

    protected $casts = [
        'show_id' => 'int',
        'comments_count' => 'int'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
