<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titre }}</title>
    <style>
        /* ── Réinitialisation ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            color: #2c3e35;
            background: #ffffff;
            line-height: 1.5;
        }

        /* ── En-tête du document ── */
        .header {
            background: #1a4731;
            color: #ffffff;
            padding: 24px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header .logo-zone {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header .logo-icon {
            width: 48px;
            height: 48px;
            background: #52b788;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .header .app-name {
            font-size: 20pt;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .header .app-sub {
            font-size: 9pt;
            color: rgba(255,255,255,0.65);
            margin-top: 2px;
        }
        .header .doc-info {
            text-align: right;
        }
        .header .doc-title {
            font-size: 13pt;
            font-weight: bold;
        }
        .header .doc-date {
            font-size: 9pt;
            color: rgba(255,255,255,0.65);
            margin-top: 4px;
        }

        /* ── Bandeau résumé ── */
        .summary-band {
            background: #d8f3dc;
            border-left: 5px solid #2d6a4f;
            padding: 14px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .summary-band .zone-name {
            font-size: 15pt;
            font-weight: bold;
            color: #1a4731;
        }
        .summary-band .zone-region {
            font-size: 10pt;
            color: #2d6a4f;
            margin-top: 2px;
        }
        .etat-badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 10pt;
            font-weight: bold;
        }
        .etat-sain     { background: #52b788; color: #ffffff; }
        .etat-degrade  { background: #f4a261; color: #ffffff; }
        .etat-critique { background: #e63946; color: #ffffff; }

        /* ── Corps principal ── */
        .body-content {
            padding: 24px 32px;
        }

        /* ── Section ── */
        .section {
            margin-bottom: 22px;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1a4731;
            border-bottom: 2px solid #d8f3dc;
            padding-bottom: 5px;
            margin-bottom: 12px;
        }

        /* ── Grille de métriques ── */
        .metrics-grid {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        .metric-card {
            background: #f8faf8;
            border: 1px solid #e2ede5;
            border-radius: 8px;
            padding: 12px 18px;
            flex: 1;
            min-width: 130px;
            text-align: center;
        }
        .metric-value {
            font-size: 18pt;
            font-weight: bold;
            color: #1a4731;
        }
        .metric-value.danger { color: #e63946; }
        .metric-value.warning { color: #e07b39; }
        .metric-label {
            font-size: 8.5pt;
            color: #6b7c72;
            margin-top: 3px;
        }

        /* ── Tableau de données ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        thead {
            background: #1a4731;
            color: #ffffff;
        }
        th {
            padding: 9px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
        }
        td {
            padding: 8px 12px;
            border-bottom: 1px solid #eef1ee;
        }
        tr:nth-child(even) td {
            background: #f8faf8;
        }

        /* ── Blocs texte ── */
        .text-block {
            background: #f8faf8;
            border: 1px solid #e2ede5;
            border-radius: 6px;
            padding: 12px 16px;
            font-size: 10.5pt;
            line-height: 1.7;
            color: #2c3e35;
        }
        .text-block.empty {
            color: #9aac9f;
            font-style: italic;
        }

        /* ── Pied de page ── */
        .footer {
            margin-top: 30px;
            border-top: 1px solid #d8f3dc;
            padding: 14px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 8.5pt;
            color: #9aac9f;
        }
        .footer .generated-by {
            font-weight: bold;
            color: #2d6a4f;
        }

        /* ── Alerte taux critique ── */
        .alert-critique {
            background: #fde;
            border: 1px solid #f5b7b1;
            border-radius: 6px;
            padding: 10px 16px;
            color: #c0392b;
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 14px;
        }

        /* ── Info auteur ── */
        .author-block {
            display: flex;
            gap: 16px;
            align-items: center;
        }
        .author-avatar {
            width: 40px;
            height: 40px;
            background: #2d6a4f;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: bold;
            font-size: 14pt;
        }
        .author-name { font-weight: bold; color: #1a4731; }
        .author-role { font-size: 9pt; color: #6b7c72; }

        /* ── Watermark ── */
        .watermark {
            position: fixed;
            bottom: 80px;
            right: 40px;
            font-size: 60pt;
            color: rgba(82,183,136,0.06);
            font-weight: bold;
            transform: rotate(-30deg);
            z-index: -1;
        }
    </style>
</head>
<body>

<!-- Watermark décoratif -->
<div class="watermark">FORESTWATCH</div>

<!-- ════════════ EN-TÊTE ════════════ -->
<div class="header">
    <div class="logo-zone">
        <div class="logo-icon">🌳</div>
        <div>
            <div class="app-name">FORESTWATCH</div>
            <div class="app-sub">Système de gestion des ressources forestières</div>
        </div>
    </div>
    <div class="doc-info">
        <div class="doc-title">Rapport d'Analyse Forestière</div>
        <div class="doc-date">Généré le {{ $date }}</div>
    </div>
</div>

<!-- ════════════ BANDEAU ZONE ════════════ -->
<div class="summary-band">
    <div>
        <div class="zone-name">{{ $zone->nom }}</div>
        <div class="zone-region">📍 {{ $zone->region }} — {{ number_format($zone->superficie, 2, ',', ' ') }} ha</div>
    </div>
    <span class="etat-badge etat-{{ str_replace('é','e', $zone->etat) }}">
        {{ ucfirst($zone->etat) }}
    </span>
</div>

<!-- ════════════ CORPS DU DOCUMENT ════════════ -->
<div class="body-content">

    <!-- ── Alerte si taux critique ── -->
    @if($analyse->taux_deforestation !== null && $analyse->taux_deforestation > 40)
    <div class="alert-critique">
        ⚠️ ALERTE : Taux de déforestation élevé ({{ $analyse->taux_deforestation }}%) — Intervention urgente recommandée
    </div>
    @endif

    <!-- ── Section 1 : Métriques clés ── -->
    <div class="section">
        <div class="section-title">📊 Indicateurs clés</div>
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-value">{{ $analyse->type_analyse }}</div>
                <div class="metric-label">Type d'analyse</div>
            </div>
            <div class="metric-card">
                @if($analyse->taux_deforestation !== null)
                    <div class="metric-value {{ $analyse->taux_deforestation > 40 ? 'danger' : ($analyse->taux_deforestation > 20 ? 'warning' : '') }}">
                        {{ $analyse->taux_deforestation }}%
                    </div>
                @else
                    <div class="metric-value">—</div>
                @endif
                <div class="metric-label">Taux de déforestation</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">
                    {{ $analyse->superficie_concernee !== null ? number_format($analyse->superficie_concernee, 1, ',', ' ') . ' ha' : '—' }}
                </div>
                <div class="metric-label">Superficie concernée</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $analyse->date_analyse->format('d/m/Y') }}</div>
                <div class="metric-label">Date de l'analyse</div>
            </div>
        </div>
    </div>

    <!-- ── Section 2 : Caractéristiques de la zone ── -->
    <div class="section">
        <div class="section-title">🌲 Caractéristiques de la zone forestière</div>
        <table>
            <thead>
                <tr>
                    <th>Paramètre</th>
                    <th>Valeur</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Nom de la zone</td>
                    <td><strong>{{ $zone->nom }}</strong></td>
                </tr>
                <tr>
                    <td>Région administrative</td>
                    <td>{{ $zone->region }}</td>
                </tr>
                <tr>
                    <td>Superficie totale</td>
                    <td>{{ number_format($zone->superficie, 2, ',', ' ') }} hectares</td>
                </tr>
                <tr>
                    <td>Coordonnées GPS</td>
                    <td>{{ $zone->latitude }}° N / {{ $zone->longitude }}° O</td>
                </tr>
                <tr>
                    <td>État de santé</td>
                    <td><strong>{{ ucfirst($zone->etat) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ── Section 3 : Espèces recensées ── -->
    @if($zone->especes && $zone->especes->count() > 0)
    <div class="section">
        <div class="section-title">🌿 Espèces d'arbres recensées ({{ $zone->especes->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th>Nom commun</th>
                    <th>Nom scientifique</th>
                    <th>Famille</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($zone->especes as $espece)
                <tr>
                    <td><strong>{{ $espece->nom_commun }}</strong></td>
                    <td><em>{{ $espece->nom_scientifique ?? '—' }}</em></td>
                    <td>{{ $espece->famille ?? '—' }}</td>
                    <td>{{ ucfirst($espece->statut ?? '—') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- ── Section 4 : Cours d'eaux ── -->
    @if($zone->coursEaux && $zone->coursEaux->count() > 0)
    <div class="section">
        <div class="section-title">💧 Cours d'eaux associés ({{ $zone->coursEaux->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Longueur (km)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($zone->coursEaux as $ce)
                <tr>
                    <td><strong>{{ $ce->nom }}</strong></td>
                    <td>{{ $ce->type ?? '—' }}</td>
                    <td>{{ $ce->longueur ? number_format($ce->longueur, 0, ',', ' ') : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- ── Section 5 : Résultats de l'analyse ── -->
    <div class="section">
        <div class="section-title">📋 Résultats de l'analyse</div>
        @if($analyse->resultat)
            <div class="text-block">{{ $analyse->resultat }}</div>
        @else
            <div class="text-block empty">Aucun résultat renseigné pour cette analyse.</div>
        @endif
    </div>

    <!-- ── Section 6 : Observations ── -->
    <div class="section">
        <div class="section-title">🔍 Observations et recommandations</div>
        @if($analyse->observations)
            <div class="text-block">{{ $analyse->observations }}</div>
        @else
            <div class="text-block empty">Aucune observation renseignée pour cette analyse.</div>
        @endif
    </div>

    <!-- ── Section 7 : Auteur ── -->
    <div class="section">
        <div class="section-title">👤 Rapport établi par</div>
        <div class="author-block">
            <div>
                <div class="author-name">{{ $user->prenom }} {{ $user->nom }}</div>
                <div class="author-role">{{ $user->role === 'admin' ? 'Administrateur' : 'Agent forestier' }}</div>
                <div class="author-role">{{ $user->email }}</div>
            </div>
        </div>
    </div>

</div><!-- /.body-content -->

<!-- ════════════ PIED DE PAGE ════════════ -->
<div class="footer">
    <div>
        <span class="generated-by">FORESTWATCH</span> — Système de surveillance et gestion des forêts de Guinée
    </div>
    <div>
        Document officiel confidentiel · {{ $date }}
    </div>
</div>

</body>
</html>
