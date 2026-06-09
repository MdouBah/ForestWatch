-- ============================================================
-- FORESTWATCH — Jeu de données de démonstration
-- Données fictives inspirées de la réalité guinéenne
-- À exécuter via : mysql -u root -h 127.0.0.1 -P 3306 forestwatch < forestwatch_seed.sql
-- ============================================================
-- Mot de passe pour tous les comptes : password
-- Admin : bah1010mad@gmail.com / password
-- ============================================================

SET FOREIGN_KEY_CHECKS=0;
TRUNCATE TABLE analyses;
TRUNCATE TABLE zone_cours_eau;
TRUNCATE TABLE zone_espece;
TRUNCATE TABLE cours_eaux;
TRUNCATE TABLE especes_arbres;
TRUNCATE TABLE zones_forestieres;
TRUNCATE TABLE rapports;
DELETE FROM users WHERE id > 0;
SET FOREIGN_KEY_CHECKS=1;

-- UTILISATEURS
INSERT INTO users (nom, prenom, email, password, role, created_at, updated_at) VALUES
('Bah',     'Mamadou',   'bah1010mad@gmail.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin',    NOW(), NOW()),
('Diallo',  'Fatoumata', 'fatoumata.diallo@fw.gn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user',     NOW(), NOW()),
('Barry',   'Alpha',     'alpha.barry@fw.gn',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'visiteur', NOW(), NOW()),
('Condé',   'Mariama',   'mariama.conde@fw.gn',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'visiteur', NOW(), NOW());

-- ZONES FORESTIÈRES (récupérer l'ID admin avec : SELECT id FROM users WHERE role='admin' LIMIT 1)
-- Remplacer @admin_id par l'ID réel si nécessaire
SET @admin_id = (SELECT id FROM users WHERE role='admin' LIMIT 1);

INSERT INTO zones_forestieres (nom, superficie, latitude, longitude, region, etat, user_id, created_at, updated_at) VALUES
('Forêt Classée de Ziama',      14200, 7.680, -8.820,  'N''Zérékoré', 'sain',    @admin_id, NOW(), NOW()),
('Réserve Naturelle de Diécké', 64500, 7.448, -8.672,  'N''Zérékoré', 'sain',    @admin_id, NOW(), NOW()),
('Forêt du Mont Nimba',         13000, 7.650, -8.420,  'N''Zérékoré', 'sain',    @admin_id, NOW(), NOW()),
('Réserve de Badiar',           36800, 12.105,-13.345, 'Boké',        'sain',    @admin_id, NOW(), NOW()),
('Forêt de Kissidougou',        18500, 9.185, -10.125, 'Kissidougou', 'sain',    @admin_id, NOW(), NOW()),
('Forêt de Pic de Fon',         25300, 8.520, -8.930,  'N''Zérékoré', 'dégradé', @admin_id, NOW(), NOW()),
('Forêt de Kambadaga',          12100, 10.520,-10.870, 'Faranah',     'dégradé', @admin_id, NOW(), NOW()),
('Forêt de Konkoumba',          7200,  10.048,-12.930, 'Kindia',      'dégradé', @admin_id, NOW(), NOW()),
('Forêt de Koumbia',            5800,  10.440,-11.990, 'Mamou',       'dégradé', @admin_id, NOW(), NOW()),
('Massif du Fouta Djallon',     22000, 11.320,-12.300, 'Labé',        'dégradé', @admin_id, NOW(), NOW()),
('Forêt de Gbéré',              8100,  7.548, -8.750,  'N''Zérékoré', 'critique', @admin_id, NOW(), NOW()),
('Forêt de Souti-Yanfou',       9500,  10.152,-10.630, 'Faranah',     'critique', @admin_id, NOW(), NOW()),
('Forêt de Kintinian',          6500,  10.870, -9.120, 'Kankan',      'critique', @admin_id, NOW(), NOW()),
('Forêt de Siguiri Nord',       4200,  11.420, -9.170, 'Siguiri',     'critique', @admin_id, NOW(), NOW()),
('Forêt de Moussaya',           3800,   9.850,-12.750, 'Kindia',      'critique', @admin_id, NOW(), NOW());

-- ESPÈCES D'ARBRES
INSERT INTO especes_arbres (nom_commun, nom_scientifique, famille, statut, description, created_at, updated_at) VALUES
('Fromager',          'Ceiba pentandra',         'Malvaceae', 'commun',  'Arbre majestueux pouvant atteindre 70 m, emblématique des forêts guinéennes.', NOW(), NOW()),
('Teck',              'Tectona grandis',          'Lamiaceae', 'commun',  'Bois dur et résistant, largement planté pour la construction.', NOW(), NOW()),
('Bambou de Guinée',  'Bambusa vulgaris',         'Poaceae',   'commun',  'Graminée géante omniprésente dans les zones humides guinéennes.', NOW(), NOW()),
('Ronier',            'Borassus aethiopum',       'Arecaceae', 'commun',  'Palmier géant caractéristique des savanes arborées du nord.', NOW(), NOW()),
('Néré',              'Parkia biglobosa',         'Fabaceae',  'commun',  'Arbre dont les graines fermentées donnent le soumbara.', NOW(), NOW()),
('Iroko',             'Milicia excelsa',          'Moraceae',  'rare',    'Surnommé l''acajou africain, très prisé pour son bois durable.', NOW(), NOW()),
('Acajou d''Afrique', 'Khaya senegalensis',       'Meliaceae', 'rare',    'Bois rouge précieux, très utilisé en menuiserie de luxe.', NOW(), NOW()),
('Caïlcédrat',        'Khaya grandifoliola',      'Meliaceae', 'rare',    'Grand arbre des galeries forestières, écorce écailleuse.', NOW(), NOW()),
('Samba',             'Triplochiton scleroxylon', 'Malvaceae', 'rare',    'Bois léger et commercial des forêts denses humides.', NOW(), NOW()),
('Vène',              'Pterocarpus erinaceus',    'Fabaceae',  'menacé',  'Arbre aux vertus médicinales reconnues, victime du braconnage végétal.', NOW(), NOW()),
('Lingué',            'Afzelia africana',         'Fabaceae',  'menacé',  'L''un des bois les plus lourds d''Afrique de l''Ouest.', NOW(), NOW()),
('Kola',              'Cola nitida',              'Malvaceae', 'menacé',  'Arbre culturellement fondamental en Guinée.', NOW(), NOW());

-- COURS D'EAUX
INSERT INTO cours_eaux (nom, type, longueur, debit, created_at, updated_at) VALUES
('Fleuve Milo',           'fleuve',  480.0, 850.0,  NOW(), NOW()),
('Fleuve Konkouré',       'fleuve',  248.0, 1400.0, NOW(), NOW()),
('Fleuve Niger (Guinée)', 'fleuve',  350.0, 1200.0, NOW(), NOW()),
('Fleuve Fatala',         'fleuve',  145.0, 320.0,  NOW(), NOW()),
('Rivière Makona',        'rivière', 120.0, 250.0,  NOW(), NOW()),
('Rivière Diani',         'rivière',  85.0, 120.0,  NOW(), NOW()),
('Rivière Kolenté',       'rivière', 180.0, 380.0,  NOW(), NOW()),
('Lac de Télimélé',       'lac',     NULL,  NULL,   NOW(), NOW());
