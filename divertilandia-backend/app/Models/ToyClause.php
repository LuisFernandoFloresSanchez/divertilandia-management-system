<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ToyClause extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relación many-to-many con Games
     */
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_toy_clause');
    }
}
