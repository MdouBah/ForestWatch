@extends('layouts.app')
@section('title', "Cours d'eaux")

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;">
    <span style="font-size:14px;color:#6b7c72;" id="count"></span>
    <button class="btn btn-primary admin-only" onclick="openModal()">+ Ajouter un cours d'eau</button>
</div>

<div id="alert-msg" class="alert"></div>

<div class="card">
    <div class="card-header"><span class="card-title">💧 Cours d'eaux</span></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nom</th><th>Type</th><th>Longueur (km)</th><th>Débit</th><th>Zones associées</th><th>Actions</th></tr></thead>
            <tbody id="table-body">
                <tr><td colspan="6" style="text-align:center;padding:30px;color:#6b7c72;">Chargement...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title" id="modal-title">Ajouter un cours d'eau</span>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="item-id">
            <div class="form-group">
                <label class="form-label">Nom *</label>
                <input type="text" id="c-nom" class="form-control" placeholder="Nom du cours d'eau">
            </div>
            <div class="grid-2col" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div class="form-group">
                    <label class="form-label">Type *</label>
                    <select id="c-type" class="form-control">
                        <option value="rivière">Rivière</option>
                        <option value="fleuve">Fleuve</option>
                        <option value="lac">Lac</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Longueur (km)</label>
                    <input type="number" id="c-longueur" class="form-control" placeholder="Longueur en km" min="0" step="0.1">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Débit (m³/s)</label>
                <input type="number" id="c-debit" class="form-control" placeholder="Débit en m³/s" min="0" step="0.01">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal()">Annuler</button>
            <button class="btn btn-primary" id="btn-save" onclick="save()">Enregistrer</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let allItems = [];
const typeEmoji = { rivière: '🏞️', fleuve: '🌊', lac: '🏔️' };

async function load() {
    const res = await apiFetch('/cours-eaux');
    allItems  = await res.json();
    document.getElementById('count').textContent = `${allItems.length} cours d'eau`;
    const tbody = document.getElementById('table-body');
    if (!allItems.length) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#6b7c72;">Aucun cours d\'eau</td></tr>';
        return;
    }
    tbody.innerHTML = allItems.map(c => `
        <tr>
            <td><strong>${typeEmoji[c.type] || '💧'} ${c.nom}</strong></td>
            <td>${c.type}</td>
            <td>${c.longueur ? c.longueur.toLocaleString('fr') : '—'}</td>
            <td>${c.debit ?? '—'}</td>
            <td style="font-size:12.5px;">${c.zones ? c.zones.map(z => z.nom).join(', ') || '—' : '—'}</td>
            <td>
                ${isAdmin() ? `<button class="btn btn-outline btn-sm" onclick="edit(${c.id})">✏️</button>
                <button class="btn btn-danger btn-sm" onclick="del(${c.id}, '${c.nom}')">🗑️</button>` : '<span style="font-size:12px;color:#6b7c72;">Lecture seule</span>'}
            </td>
        </tr>`).join('');
}

function openModal(item = null) {
    document.getElementById('item-id').value  = item ? item.id : '';
    document.getElementById('c-nom').value     = item ? item.nom : '';
    document.getElementById('c-type').value    = item ? item.type : 'rivière';
    document.getElementById('c-longueur').value= item ? (item.longueur || '') : '';
    document.getElementById('c-debit').value   = item ? (item.debit || '') : '';
    document.getElementById('modal-title').textContent = item ? 'Modifier' : "Ajouter un cours d'eau";
    document.getElementById('modal').classList.add('open');
}
function closeModal() { document.getElementById('modal').classList.remove('open'); }
function edit(id) { openModal(allItems.find(x => x.id === id)); }

async function save() {
    const id = document.getElementById('item-id').value;
    const body = {
        nom:      document.getElementById('c-nom').value,
        type:     document.getElementById('c-type').value,
        longueur: document.getElementById('c-longueur').value ? parseFloat(document.getElementById('c-longueur').value) : null,
        debit:    document.getElementById('c-debit').value ? parseFloat(document.getElementById('c-debit').value) : null,
    };
    const btn = document.getElementById('btn-save');
    btn.disabled = true; btn.textContent = 'Enregistrement...';
    const res = await apiFetch(id ? `/cours-eaux/${id}` : '/cours-eaux', {
        method: id ? 'PUT' : 'POST', body: JSON.stringify(body),
    });
    if (res.ok) { closeModal(); showAlert('Enregistré !', 'success'); load(); }
    else { const e = await res.json(); showAlert(e.message || 'Erreur', 'error'); }
    btn.disabled = false; btn.textContent = 'Enregistrer';
}

async function del(id, nom) {
    if (!confirm(`Supprimer "${nom}" ?`)) return;
    const res = await apiFetch(`/cours-eaux/${id}`, { method: 'DELETE' });
    if (res.ok) { showAlert('Supprimé.', 'success'); load(); }
}

function showAlert(msg, type) {
    const el = document.getElementById('alert-msg');
    el.textContent = msg; el.className = `alert alert-${type} show`;
    setTimeout(() => el.classList.remove('show'), 3500);
}

load();
</script>
@endpush
