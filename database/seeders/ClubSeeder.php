<?php

namespace Database\Seeders;

use App\Models\Club;
use Illuminate\Database\Seeder;

class ClubSeeder extends Seeder
{
    /**
     * Real club names from the legacy `club` table (jennv1765858.sql),
     * deduplicated. Left WITHOUT an arrondissement on purpose: the old table
     * only recorded a short internal code per club (nord1..nord7, sud1..sud11,
     * ouest1..ouest9), which doesn't map reliably to the real arrondissement
     * names in ArrondissementSeeder without local knowledge only YL staff have.
     * Assign each club's arrondissement from the backoffice as they come up.
     */
    private const CLUBS = [
        'Abraham', 'Acajou', 'Accrux', 'Adelphe', 'Adelphos', 'Adherent', 'Adonai',
        'Agapè', 'Aigle D\'Or', 'Allégresse', 'Alpha', 'Altaïr', 'Animus', 'Anti Stress',
        'Apogée', 'Astre d\'Or', 'Aurore', 'Authenthique', 'Baldarie', 'Barranquilla',
        'Belle Vision', 'Beraca (Bercy)', 'Bettsaida', 'Blouse Starts', 'Bon Semeur',
        'Booster', 'Brebis', 'Bright', 'Brillant', 'Bénignité', 'Calactique', 'Chabin',
        'Chalon', 'Club Admirable', 'Club Bon Berger', 'Club Bougeon vert', 'Club Eclat',
        'Conquerant', 'Constellation', 'Corbeau', 'Cosmopolite', 'Cosmos',
        'Croix des Matyrs', 'Cèdre', 'Des Amitiers', 'Deschapelles', 'Devoue',
        'Diamant (Savanne a Palmes)', 'Différence', 'Dream Life (Cerca Cavajal)',
        'Dubuisson (Merab)', 'Dynamique', 'Dynamo', 'Eclaireurs', 'EL Shadaï', 'Elim',
        'Elite', 'Elkai', 'Elohim', 'Energie', 'Ephata', 'Epheka', 'Ereka', 'Erudit',
        'Espérance', 'Estella Nova', 'Etincelle', 'Etincelle de pouly', 'Etoile',
        'Etoile brillante', 'Etoile Polaire (PAP)', 'Excelsior', 'Extinguible', 'Fameux',
        'Feeling Stars', 'Fiat Lux', 'Flambeau', 'Formidable', 'Forscher', 'Fortifiant',
        'Fraternité', 'Freedom', 'Fujev', 'Gaiete', 'Gaieté', 'Galactique', 'Galaxie',
        'Gosen Life', 'Génial', 'Harmonie', 'Haziel', 'Hidekel', 'Hirrondele',
        'Hors Pair', 'Ideal', 'Identique', 'Idéal', 'Impact', 'Innovateur',
        'Innovation ( Pignon)', 'Invincible', 'Joppee', 'Jupiter', 'K- Fou Ledant',
        'K-Risma', 'K-Talog', 'L\'Espoir', 'La Fleur', 'La Providence', 'La Réforme',
        'La Sagesse', 'Lacoline', 'Lael', 'Lanmou', 'Le Rocher', 'Le Roseau',
        'Les Robustes', 'Life Disipulo', 'Light', 'Lion', 'Lumière', 'Mahalaleel',
        'Malgre', 'Maranatha', 'Master', 'Modele', 'Nenuphar', 'New Life',
        'Nouvel Avenir ( Ganbade)', 'Nouvel Horizon', 'Nouvelle  vie',
        'Nouvelle Extention', 'Nouvelle Image', 'Nouvelle Vision', 'Nova',
        'Novateur (Boyer)', 'Novation', 'Odoriferant', 'OH-LALA', 'Omega', 'One Peace',
        'Onyx', 'Ophir', 'Optimiste', 'Optimum', 'Orvane', 'Oxygène', 'Passion',
        'Perfect', 'Perfection', 'Perspicace', 'Phalange', 'Phare', 'Phenix',
        'Philantrope', 'Pleiade des Jeunes', 'Positif', 'Posumus', 'Power', 'Premices',
        'Progres', 'Progressiste', 'Promoteur', 'Quintessence', 'Reformistes',
        'Renaissance', 'Renovation', 'Rocher', 'Rockstar', 'Royale de Simoreau',
        'Réformateurs', 'Réformiste', 'Référence', 'Régénération', 'Sakala', 'Saphir',
        'Scheubeu d\'Or', 'Sensation', 'Seraja', 'Shelly', 'Shiny Star', 'Siloe',
        'Sincerite', 'Sirius', 'Sky', 'Sofrim', 'Sommet', 'Soul Seeker', 'Source Club',
        'Spirit', 'Splendide', 'Spécimen', 'Standard', 'Star', 'Strong', 'Sublime',
        'Succès', 'Succès II', 'Sun', 'Super Nova', 'Suprême', 'Synergie', 'Tchelele',
        'Thomassique', 'Thomstar', 'Thomstars', 'Thérapie', 'Topaze', 'Torat',
        'Transcendance', 'Transformation', 'Triomphe', 'Umoja', 'Union', 'Uriel',
        'Vi pipip', 'Vibration', 'Victoire', 'Victory', 'Vision', 'Vision Moderne',
        'Visionnaire', 'Walhalla', 'World of Life', 'Yahweh', 'Young Workers',
        'Zénith', 'Éclair', 'Émeraude', 'Étoile',
    ];

    public function run(): void
    {
        foreach (self::CLUBS as $name) {
            Club::firstOrCreate(['name' => $name, 'arrondissement_id' => null]);
        }
    }
}
