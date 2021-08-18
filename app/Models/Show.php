<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Show extends Model
{
    use HasFactory;

    protected $with = ['genres'];

    protected $withCount = ['episodes as total_episodes'];

    protected $appends = ['total_seasons'];

    protected $casts = [
        'total_episodes' => 'int'
    ];

    protected $hidden = [
      'created_at',
      'updated_at',
    ];

    public function getTotalSeasonsAttribute()
    {
        return $this->episodes()->groupBy('season')->count();
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }
}
