<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $with = ['user'];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
