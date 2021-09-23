<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Show extends Model
{
    const UPDATED_AT = null;
    private const USER_WATCHING_STATUS = 'watching';
    private const USER_WATCHED_STATUS = 'watched';

    use HasFactory;

    protected $with = ['genres'];

    protected $withCount = ['episodes as total_episodes'];

    protected $appends = ['total_seasons', 'watch_status', 'watched_episodes', 'user_vote', 'rating'];

    protected $casts = [
        'total_episodes' => 'int',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot',
    ];

    protected $fillable = [
      'title',
      'title_original',
      'description',
      'year',
      'status',
      'imdbId',
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

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('vote');
    }

    public function getWatchedEpisodesAttribute()
    {
        if (Auth::guest()) {
            return 0;
        }

        return Auth::user()->episodes()->where('show_id', $this->id)->count();
    }

    public function getWatchStatusAttribute()
    {
        if (Auth::guest() || !$this->users()->where('user_id', Auth::id())->exists()) {
            return null;
        }

        if($this->watched_episodes < $this->total_episodes) {
            return self::USER_WATCHING_STATUS;
        }

        return self::USER_WATCHED_STATUS;
    }

    public function getUserVoteAttribute()
    {
        if (Auth::guest()) {
            return null;
        }

        return $this->users()->firstWhere('user_id', Auth::id())->pivot->vote;
    }

    public function getRatingAttribute()
    {
        return (int) round($this->users()->avg('vote'));
    }
}
