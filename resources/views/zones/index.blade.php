@extends('layouts.app')
@section('title', 'Zones forestières')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;flex-wrap:wrap;gap:12px;">
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <select id="filter-etat" class="form-control" style="width:auto;" onchange="loadZones()">
            <option value="">Tous les états</option>
            <option value="sain">Sain</option>
            <option value="dégradé">Dégradé</option>
            <option value="critique">Critique</option>
        </select>
        <select id="filter-region" class="form-control" style="width:auto;" onchange="loadZones()">
            <option value="">Toutes les régions</option>
        </select>
    </div>
    <button class="btn btn-primary admin-only" onclick="openModal()">+ Ajouter une zone</button>
</div>

<div id="alert-msg" class="alert"></div>

<div class="card">
    <div class="card-header">
        <span class="card-title">🌲 Zones forestières</span>
        <span id="zones-count" style="font-size:13px;color:#6b7c72;"></span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nom</th><th>Région</th><th>Superficie (ha)</th>
                    <th>État</th><th>Coordonnées</th><th>Actions</th>
                </tr>
            </thead>
            <tbody id="zones-table">
                <tr><td colspan="6" style="text-align:center;padding:30px;color:#6b7c72;">Chargement...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajout/Modification -->
<div class="modal-overlay" id="modal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title" id="modal-title">Ajouter une zone forestière</span>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="zone-id">
            <div class="grid-2col" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Nom de la zone *</label>
                    <input type="text" id="z-nom" class="form-control" placeholder="Nom de la zone forestière">
                </div>
                <div class="form-group">
                    <label class="form-label">Région *</label>
                    <input type="text" id="z-region" class="form-control" placeholder="Région administrative">
                </div>
                <div class="form-group">
                    <label class="form-label">Superficie (ha) *</label>
                    <input type="number" id="z-superficie" class="form-control" placeholder="Superficie en hectares" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label class="form-label">Latitude *</label>
                    <input type="number" id="z-latitude" class="form-control" placeholder="Latitude (degrés décimaux)" step="0.0001">
                </div>
                <div class="form-group">
                    <label class="form-label">Longitude *</label>
                    <input type="number" id="z-longitude" class="form-control" placeholder="Longitude (degrés décimaux)" step="0.0001">
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">État de santé *</label>
                    <select id="z-etat" class="form-control">
                        <option value="sain">Sain</option>
                        <option value="dégradé">Dégradé</option>
                        <option value="critique">Critique</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal()">Annuler</button>
            <button class="btn btn-primary" id="btn-save" onclick="saveZone()">Enregistrer</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let allZones = [];
const etatBadge = { sain: 'badge-sain', 'dégradé': 'badge-degrade', critique: 'badge-critique' };

async function loadZones() {
    const etat   = document.getElementById('filter-etat').value;
    const region = document.getElementById('filter-region').value;
    let url = '/zones?';
    if (etat)   url += `etat=${encodeURIComponent(etat)}&`;
    if (region) url += `region=${encodeURIComponent(region)}`;

    const res   = await apiFetch(url);
    allZones    = await res.json();

    // Régions
    const regions  = [...new Set(allZones.map(z => z.region))].sort();
    const selReg   = document.getElementById('filter-region');
    const current  = selReg.value;
    selReg.innerHTML = '<option value="">Toutes les régions</option>' +
        regions.map(r => `<option value="${r}" ${r===current?'selected':''}>${r}</option>`).join('');

    document.getElementById('zones-count').textContent = `${allZones.length} zone(s)`;

    const tbody = document.getElementById('zones-table');
    if (!allZones.length) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#6b7c72;">Aucune zone trouvée</td></tr>';
        return;
    }
    tbody.innerHTML = allZones.map(z => `
        <tr>
            <td><strong>${z.nom}</strong></td>
            <td>${z.region}</td>
            <td>${z.superficie.toLocaleString('fr')}</td>
            <td><span class="badge ${etatBadge[z.etat]}">${z.etat}</span></td>
            <td style="font-size:12px;color:#6b7c72;">${z.latitude}, ${z.longitude}</td>
            <td>
                ${isAdmin() ? `<button class="btn btn-outline btn-sm" onclick="editZone(${z.id})">✏️</button>
                <button class="btn btn-danger btn-sm" onclick="deleteZone(${z.id}, '${z.nom}')">🗑️</button>` : '<span style="font-size:12px;color:#6b7c72;">Lecture seule</span>'}
            </td>
        </tr>`).join('');
}

function openModal(zone = null) {
    document.getElementById('zone-id').value   = zone ? zone.id : '';
    document.getElementById('z-nom').value       = zone ? zone.nom : '';
    document.getElementById('z-region').value    = zone ? zone.region : '';
    document.getElementById('z-superficie').value= zone ? zone.superficie : '';
    document.getElementById('z-latitude').value  = zone ? zone.latitude : '';
    document.getElementById('z-longitude').value = zone ? zone.longitude : '';
    document.getElementById('z-etat').value      = zone ? zone.etat : 'sain';
    document.getElementById('modal-title').textContent = zone ? 'Modifier la zone' : 'Ajouter une zone forestière';
    document.getElementById('modal').classList.add('open');
}

function closeModal() { document.getElementById('modal').classList.remove('open'); }

function editZone(id) {
    const zone = allZones.find(z => z.id === id);
    if (zone) openModal(zone);
}

async function saveZone() {
    const id   = document.getElementById('zone-id').value;
    const body = {
        nom:        document.getElementById('z-nom').value,
        region:     document.getElementById('z-region').value,
        superficie: parseFloat(document.getElementById('z-superficie').value),
        latitude:   parseFloat(document.getElementById('z-latitude').value),
        longitude:  parseFloat(document.getElementById('z-longitude').value),
        etat:       document.getElementById('z-etat').value,
    };
    const btn = document.getElementById('btn-save');
    btn.disabled = true; btn.textContent = 'Enregistrement...';

    const res = await apiFetch(id ? `/zones/${id}` : '/zones', {
        method: id ? 'PUT' : 'POST',
        body: JSON.stringify(body),
    });

    if (res.ok) {
        closeModal();
        showAlert('Zone enregistrée avec succès !', 'success');
        loadZones();
    } else {
        const err = await res.json();
        showAlert(err.message || 'Erreur lors de l\'enregistrement.', 'error');
    }
    btn.disabled = false; btn.textContent = 'Enregistrer';
}

async function deleteZone(id, nom) {
    if (!confirm(`Supprimer la zone "${nom}" ?`)) return;
    const res = await apiFetch(`/zones/${id}`, { method: 'DELETE' });
    if (res.ok) { showAlert('Zone supprimée.', 'success'); loadZones(); }
}

function showAlert(msg, type) {
    const el = document.getElementById('alert-msg');
    el.textContent = msg;
    el.className = `alert alert-${type} show`;
    setTimeout(() => el.classList.remove('show'), 3500);
}

loadZones();
</script>
@endpush
