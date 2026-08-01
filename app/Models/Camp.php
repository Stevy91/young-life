<?php

namespace App\Models;

use App\Enums\CampStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Camp extends Model
{
    /**
     * Seeded onto every new camp so the registration form's Role field is
     * never empty — matches the legacy fixed "Role" options. Admins can
     * rename, remove, or add to these at any time from the Rôles tab.
     */
    private const DEFAULT_CATEGORIES = [
        'Campeur',
        'Campeur(jenn Manman)',
        'Responsable',
        'Conseiller',
    ];

    protected $fillable = [
        'name',
        'slug',
        'zone_id',
        'date_debut',
        'date_fin',
        'nb_nuits',
        'capacite',
        'statut',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'statut' => CampStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $camp) {
            if (blank($camp->slug)) {
                $camp->slug = static::uniqueSlugFor($camp->name);
            }
        });

        static::created(function (self $camp) {
            foreach (self::DEFAULT_CATEGORIES as $name) {
                $camp->categories()->create(['name' => $name]);
            }
        });
    }

    protected static function uniqueSlugFor(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(CampCategory::class);
    }

    /**
     * Restrict a query to camps a given user is allowed to see: their
     * assigned zone(s). Super admins see everything, unfiltered. Every camp
     * belongs to exactly one zone — there is no "national" camp.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->whereIn('zone_id', $user->zones()->pluck('zones.id'));
    }
}
