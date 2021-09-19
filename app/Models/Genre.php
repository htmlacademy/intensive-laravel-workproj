<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    use HasFactory;

    protected $visible = [
        'id',
        'title',
    ];

    protected $fillable = [
        'title',
        'title_en',
    ];

    public function shows(): BelongsToMany
    {
        return $this->belongsToMany(Show::class);
    }
}
