<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Episode extends Model
{
    use HasFactory;

    protected $withCount = ['comments'];

    protected $appends = ['watched'];

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

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function getWatchedAttribute()
    {
        return Auth::check() && $this->users()->where('user_id', Auth::id())->exists();
    }
}
