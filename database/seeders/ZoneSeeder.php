<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * The 5 Métro regions from the legacy dashboard's sidebar — this is the
     * level at which staff get menu access and camps are scoped. Order
     * matches the sidebar order given by the client.
     */
    private const ZONES = [
        "Métro l'Ouest",
        'Métro Nord-Ouest',
        'Métro Centre Et Nord-Est',
        'Métro Sud-Ouest',
        'Métro Sud-Est',
    ];

    public function run(): void
    {
        foreach (self::ZONES as $name) {
            Zone::firstOrCreate(['name' => $name]);
        }
    }
}
