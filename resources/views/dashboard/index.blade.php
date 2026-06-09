@extends('layouts.app')
@section('title', 'Tableau de bord')

@push('styles')
<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 22px;
        margin-top: 22px;
    }
    .chart-container { position: relative; height: 280px; }
    .recent-list { display: flex; flex-direction: column; gap: 10px; }
    .recent-item {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 14px;
        border-radius: 9px;
        background: #f8faf8;
        border: 1px solid #eef1ee;
        transition: background .2s;
    }
    .recent-item:hover { background: #f0f5f1; }
    .recent-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .recent-info { flex: 1; }
    .recent-name { font-size: 13.5px; font-weight: 600; color: #1a4731; }
    .recent-meta { font-size: 12px; color: #6b7c72; margin-top: 2px; }
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6b7c72;
        font-size: 14px;
    }
    .empty-state .icon { font-size: 40px; margin-bottom: 10px; }
    @media (max-width: 900px) {
        .dashboard-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<!-- Stats -->
<div class="stats-grid" id="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">🌲</div>
        <div>
            <div class="stat-value" id="stat-zones">—</div>
            <div class="stat-label">Zones forestières</div>
        </div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon">🌿</div>
        <div>
            <div class="stat-value" id="stat-especes">—</div>
            <div class="stat-label">Espèces d'arbres</div>
        </div>
    </div>
    <div class="stat-card bleu">
        <div class="stat-icon">📈</div>
        <div>
            <div class="stat-value" id="stat-analyses">—</div>
            <div class="stat-label">Analyses effectuées</div>
        </div>
    </div>
    <div class="stat-card rouge">
        <div class="stat-icon">⚠️</div>
        <div>
            <div class="stat-value" id="stat-critiques">—</div>
            <div class="stat-label">Zones critiques</div>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Graphique zones par état -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">🌍 Zones par état de santé</span>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="chart-etat"></canvas>
            </div>
        </div>
    </div>

    <!-- Analyses récentes (masqué pour visiteurs) -->
    <div class="card visiteur-hide">
        <div class="card-header">
            <span class="card-title">🕐 Analyses récentes</span>
            <a href="/analyses" class="btn btn-outline btn-sm">Voir tout</a>
        </div>
        <div class="card-body">
            <div class="recent-list" id="recent-analyses">
                <div class="empty-state"><div class="icon">📊</div>Chargement...</div>
            </div>
        </div>
    </div>

    <!-- Bienvenue visiteur (visible uniquement pour les visiteurs) -->
    <div class="card" id="card-visiteur" style="display:none;">
        <div class="card-header">
            <span class="card-title">👋 Bienvenue sur FORESTWATCH</span>
        </div>
        <div class="card-body">
            <div style="font-size:13.5px;line-height:1.8;color:#2c3e35;">
                <p style="margin-bottom:12px;">En tant que <strong>visiteur</strong>, vous pouvez :</p>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <div>✅ Consulter la <a href="/carte" style="color:#2d6a4f;font-weight:600;">carte interactive</a></div>
                    <div>✅ Explorer les <a href="/zones" style="color:#2d6a4f;font-weight:600;">zones forestières</a></div>
                    <div>✅ Voir les <a href="/especes" style="color:#2d6a4f;font-weight:600;">espèces d'arbres</a></div>
                    <div>✅ Découvrir les <a href="/cours-eaux" style="color:#2d6a4f;font-weight:600;">cours d'eaux</a></div>
                    <div style="color:#6b7c72;">🔒 Analyses et rapports (accès agent requis)</div>
                </div>
                <div style="margin-top:16px;padding:10px 14px;background:#f0f5f1;border-radius:8px;font-size:12.5px;color:#2d6a4f;border:1px solid #c1ddc8;">
                    Pour un accès complet, contactez un administrateur afin de changer votre rôle.
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique superficie par région -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">📊 Superficie par région (ha)</span>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="chart-region"></canvas>
            </div>
        </div>
    </div>

    <!-- Résumé des analyses (masqué pour visiteurs) -->
    <div class="card visiteur-hide">
        <div class="card-header">
            <span class="card-title">🔍 Résumé des analyses</span>
        </div>
        <div class="card-body">
            <div id="analyse-summary">
                <div class="empty-state"><div class="icon">📈</div>Chargement...</div>
            </div>
        </div>
    </div>

    <!-- Espèces menacées (visible pour visiteurs à la place du résumé) -->
    <div class="card" id="card-especes-visiteur" style="display:none;">
        <div class="card-header">
            <span class="card-title">⚠️ Espèces menacées</span>
            <a href="/especes" class="btn btn-outline btn-sm">Voir tout</a>
        </div>
        <div class="card-body">
            <div id="especes-menacees-list">
                <div class="empty-state"><div class="icon">🌿</div>Chargement...</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let chartEtat, chartRegion;

async function loadDashboard() {
    try {
        /* ── Données communes à tous les rôles ── */
        const [zonesRes, especesRes, analysesRes] = await Promise.all([
            apiFetch('/zones/statistiques'),
            apiFetch('/especes'),
            apiFetch('/analyses/dashboard'),
        ]);
        const zones   = await zonesRes.json();
        const especes = await especesRes.json();
        const analyses= await analysesRes.json();

        /* ── Cartes de statistiques ── */
        document.getElementById('stat-zones').textContent    = zones.total_zones ?? '—';
        document.getElementById('stat-especes').textContent  = Array.isArray(especes) ? especes.length : '—';
        document.getElementById('stat-analyses').textContent = analyses.total_analyses ?? '—';
        const critique = (zones.par_etat || []).find(e => e.etat === 'critique');
        document.getElementById('stat-critiques').textContent = critique ? critique.total : 0;

        /* ── Graphique donut : état de santé ── */
        const etats  = zones.par_etat || [];
        const colors = { sain: '#52b788', 'dégradé': '#f4a261', critique: '#e63946' };
        if (chartEtat) chartEtat.destroy();
        chartEtat = new Chart(document.getElementById('chart-etat'), {
            type: 'doughnut',
            data: {
                labels: etats.map(e => e.etat.charAt(0).toUpperCase() + e.etat.slice(1)),
                datasets: [{
                    data: etats.map(e => e.total),
                    backgroundColor: etats.map(e => colors[e.etat] || '#999'),
                    borderWidth: 2, borderColor: '#fff',
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { padding: 16, font: { size: 13 } } } }
            }
        });

        /* ── Graphique barres : superficie par région ── */
        const regions = zones.par_region || [];
        if (chartRegion) chartRegion.destroy();
        chartRegion = new Chart(document.getElementById('chart-region'), {
            type: 'bar',
            data: {
                labels: regions.map(r => r.region),
                datasets: [{
                    label: 'Superficie (ha)',
                    data: regions.map(r => r.superficie_totale),
                    backgroundColor: '#2d6a4f',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f0f3f0' } },
                    x: { grid: { display: false } }
                }
            }
        });

        /* ── Contenu adapté selon le rôle ── */
        if (isVisiteur()) {
            // Afficher les cards visiteur
            document.getElementById('card-visiteur').style.display       = 'block';
            document.getElementById('card-especes-visiteur').style.display = 'block';

            // Espèces menacées pour le visiteur
            const menacees = Array.isArray(especes)
                ? especes.filter(e => e.statut === 'menacé')
                : [];
            const listEl = document.getElementById('especes-menacees-list');
            if (!menacees.length) {
                listEl.innerHTML = '<div class="empty-state"><div class="icon">🌿</div>Aucune espèce menacée</div>';
            } else {
                listEl.innerHTML = menacees.map(e => `
                    <div class="recent-item">
                        <div class="recent-dot" style="background:#e63946;"></div>
                        <div class="recent-info">
                            <div class="recent-name">${e.nom_commun}</div>
                            <div class="recent-meta" style="font-style:italic;">${e.nom_scientifique}</div>
                        </div>
                        <span style="font-size:11px;background:#fde;color:#c0392b;padding:2px 8px;border-radius:20px;font-weight:700;">Menacé</span>
                    </div>`).join('');
            }
        } else {
            /* ── Analyses récentes (agent / admin) ── */
            const recentes = analyses.recentes || [];
            const listEl   = document.getElementById('recent-analyses');
            if (!recentes.length) {
                listEl.innerHTML = '<div class="empty-state"><div class="icon">📊</div>Aucune analyse pour l\'instant</div>';
            } else {
                listEl.innerHTML = recentes.map(a => `
                    <div class="recent-item">
                        <div class="recent-dot" style="background:${a.taux_deforestation > 30 ? '#e63946' : '#52b788'}"></div>
                        <div class="recent-info">
                            <div class="recent-name">${a.zone ? a.zone.nom : '—'}</div>
                            <div class="recent-meta">${a.type_analyse} · ${new Date(a.date_analyse).toLocaleDateString('fr-FR')}</div>
                        </div>
                        ${a.taux_deforestation != null
                            ? `<span style="font-size:13px;font-weight:700;color:${a.taux_deforestation > 30 ? '#e63946' : '#2d6a4f'}">${a.taux_deforestation}%</span>`
                            : ''}
                    </div>`).join('');
            }

            /* ── Résumé analyses ── */
            document.getElementById('analyse-summary').innerHTML = `
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:#f8faf8;border-radius:8px;">
                        <span style="font-size:13.5px;color:#6b7c72;">Total analyses</span>
                        <span style="font-size:20px;font-weight:700;color:#1a4731;">${analyses.total_analyses}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:#fde8d8;border-radius:8px;">
                        <span style="font-size:13.5px;color:#7d4a20;">Taux moy. déforestation</span>
                        <span style="font-size:20px;font-weight:700;color:#c05621;">${analyses.taux_moyen_deforestation ?? '—'}%</span>
                    </div>
                    ${(analyses.par_type || []).map(t => `
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px;border-bottom:1px solid #eef1ee;">
                        <span style="font-size:13px;color:#2c3e35;">${t.type_analyse}</span>
                        <span style="font-size:14px;font-weight:700;color:#2d6a4f;">${t.total}</span>
                    </div>`).join('')}
                </div>`;
        }

    } catch (err) {
        console.error('Erreur dashboard:', err);
    }
}

loadDashboard();
</script>
@endpush
