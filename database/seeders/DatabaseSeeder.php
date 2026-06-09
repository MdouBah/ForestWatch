<?php

namespace Database\Seeders;

use App\Models\Analyse;
use App\Models\CoursEau;
use App\Models\EspeceArbre;
use App\Models\User;
use App\Models\ZoneForestiere;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ══════════════════════════════════════════════════
         *  1. COMPTES UTILISATEURS
         * ══════════════════════════════════════════════════ */

        // Administrateur principal
        $admin = User::updateOrCreate(
            ['email' => 'bah1010mad@gmail.com'],
            [
                'nom'      => 'Bah',
                'prenom'   => 'Mamadou',
                'email'    => 'bah1010mad@gmail.com',
                'password' => Hash::make('B@@h1010@@'),
                'role'     => 'admin',
            ]
        );

        // Agent de saisie 1
        $agent1 = User::updateOrCreate(
            ['email' => 'djansow@gmail.com'],
            [
                'nom'      => 'Sow',
                'prenom'   => 'Mamadou Dian',
                'email'    => 'djansow@gmail.com',
                'password' => Hash::make('djan@@1010'),
                'role'     => 'user',
            ]
        );

        // Agent de saisie 2
        $agent2 = User::updateOrCreate(
            ['email' => 'kalidou.diallo@forestwatch.gn'],
            [
                'nom'      => 'Diallo',
                'prenom'   => 'Kalidou',
                'email'    => 'kalidou.diallo@forestwatch.gn',
                'password' => Hash::make('Kalidou@2025'),
                'role'     => 'user',
            ]
        );

        // Visiteur de démonstration
        User::updateOrCreate(
            ['email' => 'visiteur@forestwatch.gn'],
            [
                'nom'      => 'Visiteur',
                'prenom'   => 'Démo',
                'email'    => 'visiteur@forestwatch.gn',
                'password' => Hash::make('visiteur123'),
                'role'     => 'visiteur',
            ]
        );

        /* ══════════════════════════════════════════════════
         *  2. ESPÈCES D'ARBRES
         * ══════════════════════════════════════════════════ */
        $especeData = [
            ['nom_commun' => 'Teck',         'nom_scientifique' => 'Tectona grandis',       'famille' => 'Lamiaceae',   'description' => 'Arbre tropical utilisé pour son bois dur.', 'statut' => 'commun'],
            ['nom_commun' => 'Eucalyptus',   'nom_scientifique' => 'Eucalyptus globulus',   'famille' => 'Myrtaceae',   'description' => 'Arbre à croissance rapide, utilisé pour la reforestation.', 'statut' => 'commun'],
            ['nom_commun' => 'Fromager',     'nom_scientifique' => 'Ceiba pentandra',       'famille' => 'Malvaceae',   'description' => 'Grand arbre emblématique de l\'Afrique de l\'Ouest.', 'statut' => 'rare'],
            ['nom_commun' => 'Iroko',        'nom_scientifique' => 'Milicia excelsa',       'famille' => 'Moraceae',    'description' => 'Arbre précieux menacé par l\'exploitation illégale.', 'statut' => 'menacé'],
            ['nom_commun' => 'Acajou',       'nom_scientifique' => 'Khaya senegalensis',    'famille' => 'Meliaceae',   'description' => 'Bois précieux d\'Afrique, très exploité.', 'statut' => 'menacé'],
            ['nom_commun' => 'Néré',         'nom_scientifique' => 'Parkia biglobosa',      'famille' => 'Fabaceae',    'description' => 'Arbre nourricier aux usages multiples.', 'statut' => 'commun'],
            ['nom_commun' => 'Karité',       'nom_scientifique' => 'Vitellaria paradoxa',   'famille' => 'Sapotaceae',  'description' => 'Arbre à beurre, vital pour les communautés rurales.', 'statut' => 'commun'],
            ['nom_commun' => 'Cola',         'nom_scientifique' => 'Cola nitida',           'famille' => 'Malvaceae',   'description' => 'Arbre à noix de cola, valeur culturelle importante.', 'statut' => 'rare'],
            ['nom_commun' => 'Caïlcédrat',   'nom_scientifique' => 'Khaya grandifoliola',   'famille' => 'Meliaceae',   'description' => 'Grand arbre forestier à bois rouge.', 'statut' => 'menacé'],
            ['nom_commun' => 'Bambou',       'nom_scientifique' => 'Bambusa vulgaris',      'famille' => 'Poaceae',     'description' => 'Graminée géante utilisée en construction.', 'statut' => 'commun'],
        ];

        $especes = [];
        foreach ($especeData as $esp) {
            $especes[] = EspeceArbre::updateOrCreate(
                ['nom_commun' => $esp['nom_commun']],
                $esp
            );
        }

        /* ══════════════════════════════════════════════════
         *  3. COURS D'EAUX
         * ══════════════════════════════════════════════════ */
        $coursEauxData = [
            ['nom' => 'Fleuve Niger',       'type' => 'fleuve',  'longueur' => 4180],
            ['nom' => 'Rivière Konkouré',   'type' => 'rivière', 'longueur' => 248],
            ['nom' => 'Rivière Tinkisso',   'type' => 'rivière', 'longueur' => 352],
            ['nom' => 'Fleuve Sénégal',     'type' => 'fleuve',  'longueur' => 1800],
            ['nom' => 'Rivière Kolenté',    'type' => 'rivière', 'longueur' => 185],
        ];

        $coursEaux = [];
        foreach ($coursEauxData as $ce) {
            $coursEaux[] = CoursEau::updateOrCreate(['nom' => $ce['nom']], $ce);
        }

        /* ══════════════════════════════════════════════════
         *  4. ZONES FORESTIÈRES
         * ══════════════════════════════════════════════════ */
        $zonesData = [
            [
                'nom'        => 'Forêt de Ziama',
                'superficie' => 112000,
                'latitude'   => 8.5500,
                'longitude'  => -9.4500,
                'region'     => 'N\'Zérékoré',
                'etat'       => 'dégradé',
                'user_id'    => $agent1->id,
                'especes'    => [0, 2, 3, 4],   // indices dans $especes
                'cours_eaux' => [0, 2],
            ],
            [
                'nom'        => 'Réserve de Diécké',
                'superficie' => 59000,
                'latitude'   => 7.8500,
                'longitude'  => -8.6900,
                'region'     => 'N\'Zérékoré',
                'etat'       => 'sain',
                'user_id'    => $agent1->id,
                'especes'    => [0, 1, 5, 6],
                'cours_eaux' => [1],
            ],
            [
                'nom'        => 'Massif du Fouta Djallon',
                'superficie' => 750000,
                'latitude'   => 11.3500,
                'longitude'  => -12.4500,
                'region'     => 'Mamou',
                'etat'       => 'dégradé',
                'user_id'    => $agent2->id,
                'especes'    => [1, 6, 9],
                'cours_eaux' => [1, 3],
            ],
            [
                'nom'        => 'Forêt classée du Pic de Fon',
                'superficie' => 25000,
                'latitude'   => 8.5300,
                'longitude'  => -8.9000,
                'region'     => 'Beyla',
                'etat'       => 'critique',
                'user_id'    => $admin->id,
                'especes'    => [2, 3, 4, 8],
                'cours_eaux' => [0],
            ],
            [
                'nom'        => 'Mangroves de Conakry',
                'superficie' => 8500,
                'latitude'   => 9.5370,
                'longitude'  => -13.6771,
                'region'     => 'Conakry',
                'etat'       => 'critique',
                'user_id'    => $admin->id,
                'especes'    => [5, 7],
                'cours_eaux' => [4],
            ],
            [
                'nom'        => 'Forêt de Kindia',
                'superficie' => 34000,
                'latitude'   => 10.0500,
                'longitude'  => -12.8600,
                'region'     => 'Kindia',
                'etat'       => 'sain',
                'user_id'    => $agent2->id,
                'especes'    => [0, 1, 5, 9],
                'cours_eaux' => [1, 4],
            ],
        ];

        $zones = [];
        foreach ($zonesData as $zd) {
            $especesIds    = array_map(fn($i) => $especes[$i]->id, $zd['especes']);
            $coursEauxIds  = array_map(fn($i) => $coursEaux[$i]->id, $zd['cours_eaux']);
            unset($zd['especes'], $zd['cours_eaux']);

            $zone = ZoneForestiere::updateOrCreate(['nom' => $zd['nom']], $zd);
            $zone->especes()->sync($especesIds);
            $zone->coursEaux()->sync($coursEauxIds);
            $zones[] = $zone;
        }

        /* ══════════════════════════════════════════════════
         *  5. ANALYSES FORESTIÈRES
         * ══════════════════════════════════════════════════ */
        $analysesData = [
            // Ziama — dégradé
            [
                'zone_forestiere_id'   => $zones[0]->id,
                'user_id'              => $agent1->id,
                'type_analyse'         => 'Déforestation',
                'resultat'             => 'Une déforestation notable a été observée dans les secteurs nord et est de la forêt. La pression agricole et l\'exploitation illégale du bois sont les principales causes identifiées.',
                'superficie_concernee' => 18500,
                'taux_deforestation'   => 32.5,
                'observations'         => 'Mise en place urgente de points de contrôle recommandée. Reboisement prioritaire dans les zones dégradées.',
                'date_analyse'         => now()->subDays(15),
            ],
            [
                'zone_forestiere_id'   => $zones[0]->id,
                'user_id'              => $admin->id,
                'type_analyse'         => 'Biodiversité',
                'resultat'             => 'Recensement de 142 espèces végétales. Plusieurs espèces endémiques menacées par la dégradation des habitats.',
                'superficie_concernee' => 50000,
                'taux_deforestation'   => null,
                'observations'         => 'Protocole de protection des espèces menacées à renforcer. Surveillance des braconniers nécessaire.',
                'date_analyse'         => now()->subDays(30),
            ],
            // Pic de Fon — critique
            [
                'zone_forestiere_id'   => $zones[3]->id,
                'user_id'              => $admin->id,
                'type_analyse'         => 'Déforestation',
                'resultat'             => 'Situation alarmante : près de 58% de la couverture forestière a disparu en 10 ans. Mines artisanales illégales et agriculture sur brûlis sont les causes principales.',
                'superficie_concernee' => 14500,
                'taux_deforestation'   => 58.2,
                'observations'         => 'Intervention d\'urgence requise. Alerte aux autorités de conservation envoyée. Plan de réhabilitation en cours de préparation.',
                'date_analyse'         => now()->subDays(7),
            ],
            // Mangroves — critique
            [
                'zone_forestiere_id'   => $zones[4]->id,
                'user_id'              => $agent2->id,
                'type_analyse'         => 'Couverture végétale',
                'resultat'             => 'Les mangroves ont perdu 45% de leur superficie depuis 2010. L\'urbanisation non contrôlée et la pollution maritime sont les facteurs principaux.',
                'superficie_concernee' => 3825,
                'taux_deforestation'   => 45.0,
                'observations'         => 'Projet de restauration des mangroves à initier avec les communautés locales. Sensibilisation environnementale urgente.',
                'date_analyse'         => now()->subDays(5),
            ],
            // Diécké — sain
            [
                'zone_forestiere_id'   => $zones[1]->id,
                'user_id'              => $agent1->id,
                'type_analyse'         => 'Ressources en eau',
                'resultat'             => 'Les cours d\'eaux de la réserve présentent une bonne qualité. La couverture forestière assure une protection efficace des bassins versants.',
                'superficie_concernee' => null,
                'taux_deforestation'   => 5.1,
                'observations'         => 'Continuer la surveillance régulière. Maintenir les patrouilles anti-braconnage.',
                'date_analyse'         => now()->subDays(20),
            ],
            // Fouta Djallon
            [
                'zone_forestiere_id'   => $zones[2]->id,
                'user_id'              => $agent2->id,
                'type_analyse'         => 'Qualité du sol',
                'resultat'             => 'Érosion significative des sols dans les zones de déboisement. La latéritisation progressive menace la fertilité des terres.',
                'superficie_concernee' => 120000,
                'taux_deforestation'   => 27.8,
                'observations'         => 'Reboisement en espèces locales recommandé. Techniques de conservation des sols à promouvoir auprès des agriculteurs.',
                'date_analyse'         => now()->subDays(45),
            ],
        ];

        foreach ($analysesData as $ad) {
            Analyse::create($ad);
        }

        $this->command->info('✅ Base de données FORESTWATCH initialisée avec succès !');
        $this->command->info('');
        $this->command->info('Comptes créés :');
        $this->command->info('  Admin    : bah1010mad@gmail.com / B@h1010@@');
        $this->command->info('  Agent 1  : djansow@gmail.com / djan@@1010');
        $this->command->info('  Agent 2  : kalidou.diallo@forestwatch.gn / Kalidou@2025');
        $this->command->info('  Visiteur : visiteur@forestwatch.gn / visiteur123');
    }
}
