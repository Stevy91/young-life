<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Zone extends Model
{
    protected $fillable = [
        'name',
    ];

    public function arrondissements(): HasMany
    {
        return $this->hasMany(Arrondissement::class);
    }

    public function camps(): HasMany
    {
        return $this->hasMany(Camp::class);
    }

    public function clubs(): HasManyThrough
    {
        return $this->hasManyThrough(Club::class, Arrondissement::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
