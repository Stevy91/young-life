<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Seeder;

class ArrondissementSeeder extends Seeder
{
    /**
     * Zone name => its arrondissements. Ouest, Nord-Ouest, and Centre Et
     * Nord-Est come directly from the client's screenshots of the legacy
     * dropdown. Sud-Ouest/Sud-Est were sent as two identical screenshots
     * (a mistake), so that list is split here by real Haitian geography
     * (Sud-Est department's 3 arrondissements vs. the rest) — reassign
     * any of these from the Arrondissements screen if this split is wrong.
     */
    private const ZONES_WITH_ARRONDISSEMENTS = [
        "Métro l'Ouest" => [
            'ARR CROIX-DES-BOUQUETS',
            'ARR ARCAHAIE',
            'ARR DE LA GONAVE',
            'ARR PORT-AU-PRINCE',
        ],
        'Métro Nord-Ouest' => [
            'ARR SAINT-LOUIS-DU-NORD',
            'ARR PORT-DE-PAIX',
            'ARR GROS-MORNE',
            'ARR MOLE SAINT NICOLAS',
            'ARR GONAIVES',
        ],
        'Métro Centre Et Nord-Est' => [
            'ARR HINCHE',
            'ARR LASCAHOBAS',
            'ARR MIREBALAIS',
            'ARR CAP-HAITIEN',
            'ARR TROU-DU-NORD',
            'ARR SAINT-RAPHAEL',
            'ARR GRANDE RIVIERE DU NORD',
        ],
        'Métro Sud-Est' => [
            'ARR JACMEL',
            'ARR BELLE-ANSE',
            'ARR BAINET',
        ],
        'Métro Sud-Ouest' => [
            'ARR CAYES',
            'ARR AQUIN',
            'ARR JEREMIE',
            'ARR MIRAGOANE',
            'ARR LEOGANE',
            'ARR CORAIL',
            'ARR BARADERE',
            'ARR ANSE-A-VEAU',
        ],
    ];

    public function run(): void
    {
        foreach (self::ZONES_WITH_ARRONDISSEMENTS as $zoneName => $arrondissements) {
            $zone = Zone::where('name', $zoneName)->firstOrFail();

            foreach ($arrondissements as $name) {
                $zone->arrondissements()->firstOrCreate(['name' => $name]);
            }
        }
    }
}
