@extends('layouts.app')
@section('title', 'Rapports PDF')

@push('styles')
<style>
    .rapport-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
        border-left: 4px solid #2d6a4f;
        padding: 18px 20px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        transition: box-shadow .2s;
        margin-bottom: 14px;
    }
    .rapport-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.1); }
    .rapport-card.alerte { border-left-color: #e63946; }
    .rapport-icon {
        width: 48px; height: 48px;
        background: #d8f3dc;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }
    .rapport-info { flex: 1; min-width: 0; }
    .rapport-titre {
        font-size: 14px;
        font-weight: 700;
        color: #1a4731;
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .rapport-meta { font-size: 12px; color: #6b7c72; line-height: 1.9; }
    .rapport-meta strong { color: #2c3e35; }
    .rapport-actions { display: flex; gap: 8px; align-items: center; flex-shrink: 0; }

    .empty-rapports {
        text-align: center;
        padding: 60px 30px;
        color: #6b7c72;
    }
    .empty-rapports .icon { font-size: 56px; margin-bottom: 16px; }
    .empty-rapports h3 { font-size: 18px; color: #1a4731; margin-bottom: 8px; }
</style>
@endpush

@section('content')

<!-- Accès refusé (visiteur) -->
<div id="access-denied" style="display:none;">
    <div class="card" style="max-width:500px;margin:60px auto;">
        <div class="card-body" style="text-align:center;padding:50px 30px;">
            <div style="font-size:52px;margin-bottom:14px;">🔒</div>
            <h2 style="font-size:20px;color:#1a4731;margin-bottom:8px;">Accès réservé</h2>
            <p style="color:#6b7c72;font-size:14px;line-height:1.6;">
                Les rapports sont accessibles aux agents de saisie et administrateurs uniquement.
            </p>
            <a href="/dashboard" class="btn btn-primary" style="margin-top:20px;">📊 Tableau de bord</a>
        </div>
    </div>
</div>

<!-- Contenu principal -->
<div id="rapports-content">

    <div id="alert-msg" class="alert"></div>

    <!-- Statistiques -->
    <div class="stats-grid" id="rapport-stats" style="margin-bottom:24px;"></div>

    <!-- En-tête section + filtre -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:12px;">
        <p style="font-size:13.5px;color:#6b7c72;max-width:500px;">
            Retrouvez ici tous vos rapports d'analyse. Pour générer un nouveau rapport PDF,
            allez sur <a href="/analyses" style="color:#2d6a4f;font-weight:600;">Analyses</a>
            et cliquez sur le bouton 📄.
        </p>
        <div style="display:flex;gap:10px;align-items:center;">
            <select id="filter-format" class="form-control" style="width:auto;" onchange="loadRapports()">
                <option value="">Tous les formats</option>
                <option value="PDF">PDF</option>
            </select>
            <span id="count" style="font-size:13px;color:#6b7c72;"></span>
        </div>
    </div>

    <!-- Liste des rapports -->
    <div id="rapports-list">
        <div style="text-align:center;padding:40px;color:#6b7c72;">
            <div style="font-size:30px;margin-bottom:8px;">⏳</div>Chargement...
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
// ── Protéger les visiteurs ──────────────────────────────────────────────────
if (isVisiteur()) {
    document.getElementById('access-denied').style.display    = 'block';
    document.getElementById('rapports-content').style.display = 'none';
}

let allRapports = [];

// ── Chargement des rapports ──────────────────────────────────────────────────
async function loadRapports() {
    const res   = await apiFetch('/rapports');
    allRapports = await res.json();

    // Filtre côté client
    const fmt      = document.getElementById('filter-format').value;
    const filtered = fmt ? allRapports.filter(r => r.format === fmt) : allRapports;

    document.getElementById('count').textContent = `${filtered.length} rapport(s)`;

    // ── Stats ──
    const total = allRapports.length;
    const pdfs  = allRapports.filter(r => r.format === 'PDF').length;

    document.getElementById('rapport-stats').innerHTML = `
        <div class="stat-card">
            <div class="stat-icon">📄</div>
            <div><div class="stat-value">${total}</div><div class="stat-label">Total rapports</div></div>
        </div>
        <div class="stat-card bleu">
            <div class="stat-icon">📕</div>
            <div><div class="stat-value">${pdfs}</div><div class="stat-label">Rapports PDF</div></div>
        </div>
        <div class="stat-card orange">
            <div class="stat-icon">🌲</div>
            <div>
                <div class="stat-value">${[...new Set(allRapports.map(r => r.analyse?.zone_forestiere_id).filter(Boolean))].length}</div>
                <div class="stat-label">Zones couvertes</div>
            </div>
        </div>`;

    // ── Rendu liste ──
    const container = document.getElementById('rapports-list');

    if (!filtered.length) {
        container.innerHTML = `
            <div class="card">
                <div class="card-body empty-rapports">
                    <div class="icon">📄</div>
                    <h3>Aucun rapport disponible</h3>
                    <p style="font-size:13.5px;margin-bottom:18px;">
                        Pour générer un rapport, allez sur <strong>Analyses</strong>
                        et cliquez sur le bouton <strong>📄 Rapport PDF</strong>.
                    </p>
                    <a href="/analyses" class="btn btn-primary">📈 Aller aux analyses</a>
                </div>
            </div>`;
        return;
    }

    container.innerHTML = filtered.map(r => {
        const zone    = r.analyse?.zone?.nom   ?? '—';
        const region  = r.analyse?.zone?.region ?? '';
        const type    = r.analyse?.type_analyse ?? '—';
        const agent   = r.user ? `${r.user.prenom} ${r.user.nom}` : '—';
        const date    = new Date(r.created_at).toLocaleDateString('fr-FR', {
            day: '2-digit', month: 'long', year: 'numeric'
        });
        const taux    = r.analyse?.taux_deforestation;
        const alerte  = taux != null && taux > 40;

        return `
        <div class="rapport-card ${alerte ? 'alerte' : ''}">
            <div class="rapport-icon">${r.format === 'PDF' ? '📕' : '📊'}</div>

            <div class="rapport-info">
                <div class="rapport-titre" title="${r.titre}">${r.titre}</div>
                <div class="rapport-meta">
                    <span>🌲 <strong>${zone}</strong>${region ? ' — ' + region : ''}</span>
                    &nbsp;·&nbsp;
                    <span>📈 ${type}</span>
                    ${taux != null
                        ? ` &nbsp;·&nbsp; <span style="font-weight:700;color:${alerte ? '#c0392b' : '#2d6a4f'};">Taux: ${taux}%</span>`
                        : ''}
                    ${alerte ? ' <span style="color:#c0392b;font-size:11px;font-weight:700;">⚠️ CRITIQUE</span>' : ''}
                    <br>
                    <span>👤 ${agent}</span>
                    &nbsp;·&nbsp;
                    <span>📅 ${date}</span>
                    &nbsp;·&nbsp;
                    <span class="badge badge-sain">${r.format}</span>
                </div>
            </div>

            <div class="rapport-actions">
                ${r.download_url
                    ? `<a href="${r.download_url}"
                          download
                          target="_blank"
                          class="btn btn-primary btn-sm"
                          title="Télécharger le PDF (accès direct, sans authentification requise)">
                            ⬇️ Télécharger
                       </a>`
                    : `<button class="btn btn-outline btn-sm" disabled title="Fichier non disponible">
                          ⬇️ Indisponible
                       </button>`}

                <button class="btn btn-danger btn-sm"
                        title="Supprimer ce rapport"
                        onclick="deleteRapport(${r.id}, '${escHtml(r.titre)}')">
                    🗑️
                </button>
            </div>
        </div>`;
    }).join('');
}

// ── Suppression ─────────────────────────────────────────────────────────────
async function deleteRapport(id, titre) {
    if (!confirm(`Supprimer le rapport "${titre}" ?\nLe fichier PDF sera définitivement supprimé.`)) return;

    const res = await apiFetch(`/rapports/${id}`, { method: 'DELETE' });
    if (res.ok) {
        showAlert('Rapport supprimé avec succès.', 'success');
        await loadRapports();
    } else {
        const e = await res.json();
        showAlert(e.message || 'Erreur lors de la suppression.', 'error');
    }
}

// ── Utilitaires ─────────────────────────────────────────────────────────────
function escHtml(str) {
    return (str || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
}

function showAlert(msg, type) {
    const el = document.getElementById('alert-msg');
    el.innerHTML = msg;
    el.className = `alert alert-${type} show`;
    setTimeout(() => el.classList.remove('show'), 5000);
}

// ── Boot ─────────────────────────────────────────────────────────────────────
if (!isVisiteur()) loadRapports();
</script>
@endpush
