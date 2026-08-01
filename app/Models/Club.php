<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    protected $fillable = [
        'arrondissement_id',
        'name',
    ];

    public function arrondissement(): BelongsTo
    {
        return $this->belongsTo(Arrondissement::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        $zoneIds = $user->zones()->pluck('zones.id');

        return $query->where(function (Builder $q) use ($zoneIds) {
            $q->whereNull('arrondissement_id')
                ->orWhereHas('arrondissement', fn (Builder $a) => $a->whereIn('zone_id', $zoneIds));
        });
    }
}
