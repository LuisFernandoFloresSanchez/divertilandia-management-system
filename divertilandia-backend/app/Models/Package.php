<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image_base64',
        'max_age',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_age' => 'integer',
        'is_active' => 'boolean'
    ];

    // Relación many-to-many con juegos
    public function games()
    {
        return $this->belongsToMany(Game::class, 'package_games')
                    ->withPivot('quantity')
                    ->withTimestamps()
                    ->with('toyType');
    }

    // Relación con juegos incluyendo información del tipo de juguete
    public function gamesWithToyTypes()
    {
        return $this->belongsToMany(Game::class, 'package_games')
                    ->withPivot('quantity')
                    ->withTimestamps()
                    ->with('toyType');
    }

}
