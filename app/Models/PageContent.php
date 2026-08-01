<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PageContent extends Model
{
    protected $fillable = [
        'page',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    /**
     * One row per public page (home, leve-fonds, a-propos, galerie),
     * matching the same "no new table per new thing" pattern as Setting —
     * a new page just needs a new row, not a new migration.
     */
    public static function forPage(string $page): self
    {
        return static::firstOrCreate(['page' => $page], ['data' => []]);
    }

    /**
     * Every image path in `data` — whether pre-seeded or saved by a
     * Filament FileUpload — lives on the "public" disk (storage/app/public),
     * which is also what FileUpload's own hydration checks against; a plain
     * public/images/... path wouldn't be found there and would silently
     * disappear the next time the page is saved.
     */
    public static function imageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
