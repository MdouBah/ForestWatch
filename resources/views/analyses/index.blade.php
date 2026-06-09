@extends('layouts.app')
@section('title', 'Analyses forestières')

@push('styles')
<style>
    .taux-bar-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .taux-bar {
        flex: 1;
        height: 6px;
        background: #e8ede9;
        border-radius: 10px;
        overflow: hidden;
        max-width: 80px;
    }
    .taux-bar-fill {
        height: 100%;
        border-radius: 10px;
        transition: width .4s;
    }
    .detail-panel {
        background: #f8faf8;
        border: 1px solid #eef1ee;
        border-radius: 10px;
        padding: 16px 20px;
        margin-top: 8px;
        display: none;
        font-size: 13px;
        line-height: 1.8;
        color: #2c3e35;
    }
    .detail-panel.open { display: block; }
    .detail-label { font-weight: 700; color: #1a4731; min-width: 160px; display: inline-block; }
    .filter-bar {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 22px;
    }
    .action-group { display: flex; gap: 6px; }
    .modal-lg { max-width: 680px; }
    textarea.form-control { resize: vertical; }
</style>
@endpush

@section('content')

<!-- Accès refusé pour les visiteurs -->
<div id="access-denied" style="display:none;">
    <div class="card" style="max-width:500px;margin:60px auto;">
        <div class="card-body" style="text-align:center;padding:50px 30px;">
            <div style="font-size:52px;margin-bottom:14px;">🔒</div>
            <h2 style="font-size:20px;color:#1a4731;margin-bottom:8px;">Accès réservé</h2>
            <p style="color:#6b7c72;font-size:14px;line-height:1.6;">
                Les analyses forestières sont accessibles aux agents de saisie et aux administrateurs uniquement.<br>
                Contactez un administrateur pour obtenir un accès.
            </p>
            <a href="/carte" class="btn btn-primary" style="margin-top:20px;">🗺️ Voir la carte</a>
        </div>
    </div>
</div>

<!-- Contenu principal -->
<div id="analyses-content">

    <!-- Barre de filtres et actions -->
    <div class="filter-bar">
        <select id="filter-zone" class="form-control" style="width:auto;min-width:200px;" onchange="loadAnalyses()">
            <option value="">Toutes les zones</option>
        </select>
        <select id="filter-type" class="form-control" style="width:auto;" onchange="loadAnalyses()">
            <option value="">Tous les types</option>
            <option value="Déforestation">Déforestation</option>
            <option value="Biodiversité">Biodiversité</option>
            <option value="Couverture végétale">Couverture végétale</option>
            <option value="Qualité du sol">Qualité du sol</option>
            <option value="Ressources en eau">Ressources en eau</option>
        </select>
        <div style="margin-left:auto;">
            <button class="btn btn-primary" onclick="openModal()">+ Nouvelle analyse</button>
        </div>
    </div>

    <div id="alert-msg" class="alert"></div>

    <!-- Statistiques rapides -->
    <div class="stats-grid" id="analyse-stats" style="margin-bottom:22px;"></div>

    <!-- Tableau des analyses -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">📈 Historique des analyses</span>
            <span id="count" style="font-size:13px;color:#6b7c72;"></span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Zone</th>
                        <th>Type d'analyse</th>
                        <th>Taux déforestation</th>
                        <th>Superficie (ha)</th>
                        <th>Agent</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="analyses-table">
                    <tr>
                        <td colspan="7" style="text-align:center;padding:30px;color:#6b7c72;">
                            <div style="font-size:30px;margin-bottom:8px;">⏳</div>Chargement...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div><!-- /#analyses-content -->


{{-- ══════════════════════════ MODAL CRÉER / MODIFIER ══════════════════════════ --}}
<div class="modal-overlay" id="modal">
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title" id="modal-title">📝 Nouvelle analyse</span>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="analyse-id">
            <div id="modal-alert" class="alert"></div>

            <div class="form-group">
                <label class="form-label">Zone forestière <span style="color:#e63946;">*</span></label>
                <select id="a-zone" class="form-control" required></select>
            </div>

            <div class="form-group">
                <label class="form-label">Type d'analyse <span style="color:#e63946;">*</span></label>
                <select id="a-type" class="form-control" required>
                    <option value="Déforestation">Déforestation</option>
                    <option value="Biodiversité">Biodiversité</option>
                    <option value="Couverture végétale">Couverture végétale</option>
                    <option value="Qualité du sol">Qualité du sol</option>
                    <option value="Ressources en eau">Ressources en eau</option>
                </select>
            </div>

            <div class="grid-2col" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div class="form-group">
                    <label class="form-label">Taux de déforestation (%)</label>
                    <input type="number" id="a-taux" class="form-control"
                           placeholder="0 – 100" min="0" max="100" step="0.1">
                </div>
                <div class="form-group">
                    <label class="form-label">Superficie concernée (ha)</label>
                    <input type="number" id="a-superficie" class="form-control"
                           placeholder="Hectares" min="0" step="0.01">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Résultats de l'analyse</label>
                <textarea id="a-resultat" class="form-control" rows="3"
                          placeholder="Résumé des résultats observés..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Observations et recommandations</label>
                <textarea id="a-observations" class="form-control" rows="3"
                          placeholder="Observations détaillées, mesures correctives proposées..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal()">Annuler</button>
            <button class="btn btn-primary" id="btn-save" onclick="saveAnalyse()">
                💾 Enregistrer
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════ MODAL DÉTAIL ══════════════════════════ --}}
<div class="modal-overlay" id="modal-detail">
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title" id="detail-title">Détail de l'analyse</span>
            <button class="modal-close" onclick="closeDetailModal()">×</button>
        </div>
        <div class="modal-body" id="detail-body">Chargement...</div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeDetailModal()">Fermer</button>
            <button class="btn btn-primary" id="btn-gen-pdf" onclick="genererPDF()">
                📄 Générer le rapport PDF
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Protéger les visiteurs ──────────────────────────────────────────────────
if (isVisiteur()) {
    document.getElementById('access-denied').style.display  = 'block';
    document.getElementById('analyses-content').style.display = 'none';
}

let allZones    = [];
let allAnalyses = [];
let currentAnalyseId = null;   // Pour la modale détail / génération PDF

// ── Initialisation ──────────────────────────────────────────────────────────
async function init() {
    const resZ = await apiFetch('/zones');
    allZones   = await resZ.json();

    // Peupler les <select> de zones
    const optionsHtml = allZones.map(z => `<option value="${z.id}">${z.nom} (${z.region})</option>`).join('');
    document.getElementById('filter-zone').innerHTML =
        '<option value="">Toutes les zones</option>' + optionsHtml;
    document.getElementById('a-zone').innerHTML = optionsHtml;

    // Pré-sélection depuis URL ?zone=
    const params = new URLSearchParams(window.location.search);
    const zId = params.get('zone');
    if (zId) {
        document.getElementById('filter-zone').value = zId;
        document.getElementById('a-zone').value = zId;
    }

    await loadAnalyses();
}

// ── Charger les analyses ────────────────────────────────────────────────────
async function loadAnalyses() {
    const zoneId = document.getElementById('filter-zone').value;
    const typeVal = document.getElementById('filter-type').value;
    let url = '/analyses';
    const params = [];
    if (zoneId) params.push(`zone_id=${zoneId}`);
    if (url.includes('?')) url += '&' + params.join('&');
    else if (params.length)  url += '?' + params.join('&');

    const res = await apiFetch(url);
    let data  = await res.json();

    // Filtre type côté client (l'API ne filtre pas encore par type)
    if (typeVal) data = data.filter(a => a.type_analyse === typeVal);
    allAnalyses = data;

    document.getElementById('count').textContent = `${allAnalyses.length} analyse(s)`;

    // ── Stats rapides ──
    const total     = allAnalyses.length;
    const tauxMoyen = total
        ? (allAnalyses.filter(a => a.taux_deforestation != null)
            .reduce((s, a) => s + a.taux_deforestation, 0) /
           (allAnalyses.filter(a => a.taux_deforestation != null).length || 1)).toFixed(1)
        : 0;
    const critiques = allAnalyses.filter(a => a.taux_deforestation > 40).length;

    document.getElementById('analyse-stats').innerHTML = `
        <div class="stat-card">
            <div class="stat-icon">📈</div>
            <div><div class="stat-value">${total}</div><div class="stat-label">Total analyses</div></div>
        </div>
        <div class="stat-card orange">
            <div class="stat-icon">📊</div>
            <div><div class="stat-value">${tauxMoyen}%</div><div class="stat-label">Taux moy. déforestation</div></div>
        </div>
        <div class="stat-card rouge">
            <div class="stat-icon">⚠️</div>
            <div><div class="stat-value">${critiques}</div><div class="stat-label">Situations critiques (&gt;40%)</div></div>
        </div>
        <div class="stat-card bleu">
            <div class="stat-icon">🌍</div>
            <div><div class="stat-value">${[...new Set(allAnalyses.map(a => a.zone_forestiere_id))].length}</div><div class="stat-label">Zones analysées</div></div>
        </div>`;

    // ── Tableau ──
    const tbody = document.getElementById('analyses-table');
    if (!allAnalyses.length) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:40px;color:#6b7c72;">
            <div style="font-size:36px;margin-bottom:10px;">📊</div>
            Aucune analyse trouvée. Créez votre première analyse !
        </td></tr>`;
        return;
    }

    tbody.innerHTML = allAnalyses.map(a => {
        const taux = a.taux_deforestation;
        let tColor, tBg;
        if (taux == null)   { tColor = '#6b7c72'; tBg = '#e8ede9'; }
        else if (taux > 40) { tColor = '#c0392b'; tBg = '#e63946'; }
        else if (taux > 20) { tColor = '#e07b39'; tBg = '#f4a261'; }
        else                { tColor = '#2d6a4f'; tBg = '#52b788'; }

        const agent = a.user ? `${a.user.prenom} ${a.user.nom}` : '—';

        return `
        <tr>
            <td><strong style="color:#1a4731;">${a.zone ? a.zone.nom : '—'}</strong>
                ${a.zone ? `<br><span style="font-size:11px;color:#6b7c72;">${a.zone.region}</span>` : ''}
            </td>
            <td>
                <span style="font-size:12px;background:#e8f4ea;color:#1a4731;padding:3px 9px;border-radius:20px;font-weight:600;">
                    ${a.type_analyse}
                </span>
            </td>
            <td>
                ${taux != null ? `
                <div class="taux-bar-wrap">
                    <span style="font-weight:700;color:${tColor};min-width:38px;">${taux}%</span>
                    <div class="taux-bar">
                        <div class="taux-bar-fill" style="width:${taux}%;background:${tBg};"></div>
                    </div>
                </div>` : '<span style="color:#9aac9f;">—</span>'}
            </td>
            <td>${a.superficie_concernee ? a.superficie_concernee.toLocaleString('fr') + ' ha' : '<span style="color:#9aac9f;">—</span>'}</td>
            <td style="font-size:12.5px;">${agent}</td>
            <td style="font-size:12px;color:#6b7c72;white-space:nowrap;">${new Date(a.date_analyse).toLocaleDateString('fr-FR')}</td>
            <td>
                <div class="action-group">
                    <button class="btn btn-outline btn-sm" title="Voir le détail" onclick="openDetail(${a.id})">🔍</button>
                    <button class="btn btn-outline btn-sm" title="Modifier" onclick="editAnalyse(${a.id})">✏️</button>
                    <button class="btn btn-primary btn-sm" title="Générer rapport PDF" onclick="genererPDFDirect(${a.id})">📄</button>
                    <button class="btn btn-danger btn-sm" title="Supprimer" onclick="deleteAnalyse(${a.id})">🗑️</button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

// ── Modale Créer / Modifier ─────────────────────────────────────────────────
function openModal(analyse = null) {
    document.getElementById('analyse-id').value      = analyse ? analyse.id : '';
    document.getElementById('a-zone').value           = analyse ? analyse.zone_forestiere_id : (allZones[0]?.id || '');
    document.getElementById('a-type').value           = analyse ? analyse.type_analyse : 'Déforestation';
    document.getElementById('a-taux').value           = analyse ? (analyse.taux_deforestation ?? '') : '';
    document.getElementById('a-superficie').value     = analyse ? (analyse.superficie_concernee ?? '') : '';
    document.getElementById('a-resultat').value       = analyse ? (analyse.resultat ?? '') : '';
    document.getElementById('a-observations').value   = analyse ? (analyse.observations ?? '') : '';
    document.getElementById('modal-title').textContent = analyse ? '✏️ Modifier l\'analyse' : '📝 Nouvelle analyse';

    const alertEl = document.getElementById('modal-alert');
    alertEl.className = 'alert'; alertEl.textContent = '';

    document.getElementById('modal').classList.add('open');
}

function closeModal() { document.getElementById('modal').classList.remove('open'); }

function editAnalyse(id) {
    const a = allAnalyses.find(x => x.id === id);
    if (a) openModal(a);
}

async function saveAnalyse() {
    const id    = document.getElementById('analyse-id').value;
    const zoneId = document.getElementById('a-zone').value;

    if (!zoneId) {
        showModalAlert('Veuillez sélectionner une zone forestière.', 'error'); return;
    }

    const body = {
        zone_forestiere_id:    parseInt(zoneId),
        type_analyse:          document.getElementById('a-type').value,
        taux_deforestation:    document.getElementById('a-taux').value
                               ? parseFloat(document.getElementById('a-taux').value) : null,
        superficie_concernee:  document.getElementById('a-superficie').value
                               ? parseFloat(document.getElementById('a-superficie').value) : null,
        resultat:              document.getElementById('a-resultat').value || null,
        observations:          document.getElementById('a-observations').value || null,
    };

    const btn = document.getElementById('btn-save');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Enregistrement...';

    const res = await apiFetch(id ? `/analyses/${id}` : '/analyses', {
        method: id ? 'PUT' : 'POST',
        body: JSON.stringify(body),
    });

    const data = await res.json();

    if (res.ok) {
        closeModal();
        showAlert(id ? 'Analyse modifiée avec succès !' : 'Analyse enregistrée avec succès !', 'success');
        await loadAnalyses();
    } else {
        const msg = data.errors
            ? Object.values(data.errors).flat().join(' · ')
            : (data.message || 'Erreur lors de l\'enregistrement.');
        showModalAlert(msg, 'error');
    }

    btn.disabled = false;
    btn.innerHTML = '💾 Enregistrer';
}

// ── Modale Détail ───────────────────────────────────────────────────────────
async function openDetail(id) {
    currentAnalyseId = id;
    document.getElementById('modal-detail').classList.add('open');
    document.getElementById('detail-body').innerHTML = '<div style="text-align:center;padding:30px;"><div class="spinner" style="border-color:rgba(0,0,0,.2);border-top-color:#2d6a4f;width:28px;height:28px;display:inline-block;"></div></div>';

    const res    = await apiFetch(`/analyses/${id}`);
    const a      = await res.json();

    document.getElementById('detail-title').textContent = `🔍 Analyse — ${a.zone?.nom ?? 'Zone inconnue'}`;

    const taux = a.taux_deforestation;
    const tColor = taux == null ? '#6b7c72' : taux > 40 ? '#c0392b' : taux > 20 ? '#e07b39' : '#2d6a4f';

    document.getElementById('detail-body').innerHTML = `
        <div class="grid-2col" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;">
            <div style="background:#f8faf8;border-radius:8px;padding:14px;">
                <div style="font-size:11px;color:#6b7c72;margin-bottom:4px;">ZONE</div>
                <div style="font-size:15px;font-weight:700;color:#1a4731;">${a.zone?.nom ?? '—'}</div>
                <div style="font-size:12px;color:#6b7c72;">${a.zone?.region ?? ''}</div>
            </div>
            <div style="background:#f8faf8;border-radius:8px;padding:14px;">
                <div style="font-size:11px;color:#6b7c72;margin-bottom:4px;">TYPE</div>
                <div style="font-size:15px;font-weight:700;color:#1a4731;">${a.type_analyse}</div>
                <div style="font-size:12px;color:#6b7c72;">${new Date(a.date_analyse).toLocaleDateString('fr-FR', {day:'2-digit',month:'long',year:'numeric'})}</div>
            </div>
            <div style="background:#f8faf8;border-radius:8px;padding:14px;">
                <div style="font-size:11px;color:#6b7c72;margin-bottom:4px;">TAUX DÉFORESTATION</div>
                <div style="font-size:22px;font-weight:800;color:${tColor};">${taux != null ? taux + '%' : '—'}</div>
            </div>
            <div style="background:#f8faf8;border-radius:8px;padding:14px;">
                <div style="font-size:11px;color:#6b7c72;margin-bottom:4px;">SUPERFICIE CONCERNÉE</div>
                <div style="font-size:22px;font-weight:800;color:#1a4731;">${a.superficie_concernee != null ? a.superficie_concernee.toLocaleString('fr') + ' ha' : '—'}</div>
            </div>
        </div>

        ${a.resultat ? `
        <div style="margin-bottom:14px;">
            <div style="font-size:12px;font-weight:700;color:#1a4731;margin-bottom:6px;">📋 Résultats</div>
            <div style="background:#f8faf8;border:1px solid #eef1ee;border-radius:7px;padding:12px;font-size:13.5px;line-height:1.8;">${a.resultat}</div>
        </div>` : ''}

        ${a.observations ? `
        <div style="margin-bottom:14px;">
            <div style="font-size:12px;font-weight:700;color:#1a4731;margin-bottom:6px;">🔍 Observations</div>
            <div style="background:#f8faf8;border:1px solid #eef1ee;border-radius:7px;padding:12px;font-size:13.5px;line-height:1.8;">${a.observations}</div>
        </div>` : ''}

        <div style="display:flex;align-items:center;gap:8px;padding:10px;background:#e8f4ea;border-radius:8px;font-size:12.5px;">
            <span>👤</span>
            <span>Analyse saisie par <strong>${a.user ? a.user.prenom + ' ' + a.user.nom : '—'}</strong>
            (${a.user?.role === 'admin' ? 'Administrateur' : 'Agent forestier'})</span>
        </div>
    `;
}

function closeDetailModal() {
    document.getElementById('modal-detail').classList.remove('open');
    currentAnalyseId = null;
}

// ── Génération PDF ──────────────────────────────────────────────────────────
async function genererPDF() {
    if (!currentAnalyseId) return;
    await _doGenererPDF(currentAnalyseId);
    closeDetailModal();
}

async function genererPDFDirect(id) {
    await _doGenererPDF(id);
}

async function _doGenererPDF(analyseId) {
    showAlert('⏳ Génération du rapport PDF en cours...', 'success');

    const res  = await apiFetch(`/rapports/generer/${analyseId}`, { method: 'POST' });
    const data = await res.json();

    if (res.ok) {
        // data.download_url = URL publique directe → pas besoin du token JWT
        // ex: http://localhost:8000/storage/rapports/rapport_xxx.pdf
        const url = data.download_url;

        // Ouvrir le PDF dans un nouvel onglet automatiquement
        if (url) {
            window.open(url, '_blank');
        }

        showAlert(
            `✅ Rapport PDF généré avec succès !
             <a href="${url}" target="_blank" download
                style="color:#1a4731;font-weight:700;text-decoration:underline;margin-left:8px;">
                ⬇️ Télécharger
             </a>
             &nbsp;·&nbsp;
             <a href="/rapports" style="color:#2d6a4f;font-weight:700;text-decoration:underline;">
                Voir tous mes rapports
             </a>`,
            'success'
        );
    } else {
        showAlert('❌ ' + (data.message || 'Erreur lors de la génération du PDF.'), 'error');
    }
}

// ── Suppression ─────────────────────────────────────────────────────────────
async function deleteAnalyse(id) {
    const a = allAnalyses.find(x => x.id === id);
    const zoneName = a?.zone?.nom || `#${id}`;
    if (!confirm(`Supprimer l'analyse sur "${zoneName}" ? Cette action est irréversible.`)) return;

    const res = await apiFetch(`/analyses/${id}`, { method: 'DELETE' });
    if (res.ok) {
        showAlert('Analyse supprimée.', 'success');
        await loadAnalyses();
    } else {
        const e = await res.json();
        showAlert(e.message || 'Erreur lors de la suppression.', 'error');
    }
}

// ── Alertes ──────────────────────────────────────────────────────────────────
function showAlert(msg, type) {
    const el = document.getElementById('alert-msg');
    el.innerHTML = msg;
    el.className = `alert alert-${type} show`;
    setTimeout(() => el.classList.remove('show'), 6000);
}

function showModalAlert(msg, type) {
    const el = document.getElementById('modal-alert');
    el.textContent = msg;
    el.className = `alert alert-${type} show`;
}

// ── Boot ─────────────────────────────────────────────────────────────────────
if (!isVisiteur()) init();
</script>
@endpush
