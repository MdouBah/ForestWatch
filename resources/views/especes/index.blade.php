@extends('layouts.app')
@section('title', "Espèces d'arbres")

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;">
    <select id="filter-statut" class="form-control" style="width:auto;" onchange="loadEspeces()">
        <option value="">Tous les statuts</option>
        <option value="commun">Commun</option>
        <option value="rare">Rare</option>
        <option value="menacé">Menacé</option>
    </select>
    <button class="btn btn-primary admin-only" onclick="openModal()">+ Ajouter une espèce</button>
</div>

<div id="alert-msg" class="alert"></div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:18px;" id="especes-grid">
    <div style="text-align:center;padding:40px;color:#6b7c72;grid-column:1/-1;">Chargement...</div>
</div>

<!-- Modal -->
<div class="modal-overlay" id="modal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title" id="modal-title">Ajouter une espèce</span>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="espece-id">
            <div class="form-group">
                <label class="form-label">Nom commun *</label>
                <input type="text" id="e-nom-commun" class="form-control" placeholder="Nom usuel de l'espèce">
            </div>
            <div class="form-group">
                <label class="form-label">Nom scientifique *</label>
                <input type="text" id="e-nom-sci" class="form-control" placeholder="Genre espèce (latin)">
            </div>
            <div class="grid-2col" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div class="form-group">
                    <label class="form-label">Famille *</label>
                    <input type="text" id="e-famille" class="form-control" placeholder="Famille botanique">
                </div>
                <div class="form-group">
                    <label class="form-label">Statut *</label>
                    <select id="e-statut" class="form-control">
                        <option value="commun">Commun</option>
                        <option value="rare">Rare</option>
                        <option value="menacé">Menacé</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea id="e-description" class="form-control" rows="3" placeholder="Description de l'espèce..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal()">Annuler</button>
            <button class="btn btn-primary" id="btn-save" onclick="saveEspece()">Enregistrer</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let allEspeces = [];
const statutBadge  = { commun: 'badge-commun', rare: 'badge-rare', 'menacé': 'badge-menace' };
const statutEmoji  = { commun: '🌿', rare: '🌱', 'menacé': '⚠️' };
const statutColors = { commun: '#1a5276', rare: '#a04000', 'menacé': '#922b21' };
const bgColors     = { commun: '#d6eaf8', rare: '#fde8d8', 'menacé': '#fde' };

async function loadEspeces() {
    const statut = document.getElementById('filter-statut').value;
    let url = '/especes' + (statut ? `?statut=${encodeURIComponent(statut)}` : '');
    const res  = await apiFetch(url);
    allEspeces = await res.json();

    const grid = document.getElementById('especes-grid');
    if (!allEspeces.length) {
        grid.innerHTML = '<div style="text-align:center;padding:40px;color:#6b7c72;grid-column:1/-1;">Aucune espèce trouvée</div>';
        return;
    }
    grid.innerHTML = allEspeces.map(e => `
        <div class="card" style="padding:0;overflow:hidden;">
            <div style="height:6px;background:${bgColors[e.statut]};"></div>
            <div style="padding:18px;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                    <span style="font-size:28px;">${statutEmoji[e.statut]}</span>
                    <span class="badge ${statutBadge[e.statut]}">${e.statut}</span>
                </div>
                <div style="font-size:16px;font-weight:700;color:#1a4731;margin-bottom:3px;">${e.nom_commun}</div>
                <div style="font-size:12px;color:#6b7c72;font-style:italic;margin-bottom:8px;">${e.nom_scientifique}</div>
                <div style="font-size:12.5px;color:#6b7c72;margin-bottom:4px;">Famille : <strong style="color:#2c3e35;">${e.famille}</strong></div>
                ${e.description ? `<div style="font-size:12.5px;color:#6b7c72;margin-top:8px;line-height:1.5;border-top:1px solid #f0f3f0;padding-top:8px;">${e.description.slice(0,100)}${e.description.length>100?'…':''}</div>` : ''}
                <div style="display:flex;gap:8px;margin-top:14px;">
                    ${isAdmin() ? `
                    <button class="btn btn-outline btn-sm" style="flex:1;" onclick="editEspece(${e.id})">✏️ Modifier</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteEspece(${e.id}, '${e.nom_commun}')">🗑️</button>
                    ` : `<span style="font-size:12px;color:#6b7c72;align-self:center;">Consultation uniquement</span>`}
                </div>
            </div>
        </div>`).join('');
}

function openModal(espece = null) {
    document.getElementById('espece-id').value     = espece ? espece.id : '';
    document.getElementById('e-nom-commun').value  = espece ? espece.nom_commun : '';
    document.getElementById('e-nom-sci').value     = espece ? espece.nom_scientifique : '';
    document.getElementById('e-famille').value     = espece ? espece.famille : '';
    document.getElementById('e-statut').value      = espece ? espece.statut : 'commun';
    document.getElementById('e-description').value = espece ? (espece.description || '') : '';
    document.getElementById('modal-title').textContent = espece ? 'Modifier l\'espèce' : 'Ajouter une espèce';
    document.getElementById('modal').classList.add('open');
}

function closeModal() { document.getElementById('modal').classList.remove('open'); }

function editEspece(id) {
    const e = allEspeces.find(x => x.id === id);
    if (e) openModal(e);
}

async function saveEspece() {
    const id   = document.getElementById('espece-id').value;
    const body = {
        nom_commun:       document.getElementById('e-nom-commun').value,
        nom_scientifique: document.getElementById('e-nom-sci').value,
        famille:          document.getElementById('e-famille').value,
        statut:           document.getElementById('e-statut').value,
        description:      document.getElementById('e-description').value,
    };
    const btn = document.getElementById('btn-save');
    btn.disabled = true; btn.textContent = 'Enregistrement...';

    const res = await apiFetch(id ? `/especes/${id}` : '/especes', {
        method: id ? 'PUT' : 'POST',
        body: JSON.stringify(body),
    });

    if (res.ok) {
        closeModal(); showAlert('Espèce enregistrée !', 'success'); loadEspeces();
    } else {
        const err = await res.json();
        showAlert(err.message || 'Erreur.', 'error');
    }
    btn.disabled = false; btn.textContent = 'Enregistrer';
}

async function deleteEspece(id, nom) {
    if (!confirm(`Supprimer "${nom}" ?`)) return;
    const res = await apiFetch(`/especes/${id}`, { method: 'DELETE' });
    if (res.ok) { showAlert('Espèce supprimée.', 'success'); loadEspeces(); }
}

function showAlert(msg, type) {
    const el = document.getElementById('alert-msg');
    el.textContent = msg;
    el.className = `alert alert-${type} show`;
    setTimeout(() => el.classList.remove('show'), 3500);
}

loadEspeces();
</script>
@endpush
