<?php

namespace App\Models;

use App\Enums\RegistrationStatut;
use App\Enums\Sexe;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    protected $fillable = [
        'camp_id',
        'camp_category_id',
        'nom',
        'adresse',
        'telephone',
        'nif_cin',
        'email',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'club_id',
        'arrondissement_id',
        'role_responsable',
        'statut',
        'leader',
        'campus',
        'adresse_campus',
        'camp_de_jour',
        'type_camp',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
            'sexe' => Sexe::class,
            'statut' => RegistrationStatut::class,
            'camp_de_jour' => 'boolean',
        ];
    }

    public function camp(): BelongsTo
    {
        return $this->belongsTo(Camp::class);
    }

    public function campCategory(): BelongsTo
    {
        return $this->belongsTo(CampCategory::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function arrondissement(): BelongsTo
    {
        return $this->belongsTo(Arrondissement::class);
    }

    /**
     * Restrict a query to registrations belonging to a camp the given user can
     * see (see Camp::scopeVisibleTo for the underlying zone rule).
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->whereHas('camp', fn (Builder $q) => $q->visibleTo($user));
    }
}
