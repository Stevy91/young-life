<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Zones this user is allowed to manage. Empty for a Super Admin (full access to all zones).
     */
    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * A user can end up with more than one of these roles at once (e.g. an
     * admin tries a few roles on a test account without unchecking the
     * previous ones), so these follow a fixed precedence — highest
     * privilege wins — rather than letting "Lecteur" veto everything:
     * Gestionnaire Zone (or no role at all) > Agent d'inscription > Lecteur.
     */
    public function isReadOnly(): bool
    {
        return $this->hasRole('lecteur')
            && ! $this->hasRole('gestionnaire_zone')
            && ! $this->hasRole('agent_inscription');
    }

    /**
     * "Agent d'inscription" can only add new registrations (Ajouter) — it's
     * the "create" ability separated from "update"/"delete", which stay
     * limited to "Gestionnaire Zone" (see RegistrationPolicy).
     */
    public function canManageRegistrations(): bool
    {
        if ($this->hasRole('gestionnaire_zone')) {
            return true;
        }

        return ! $this->hasRole('lecteur') && ! $this->hasRole('agent_inscription');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
