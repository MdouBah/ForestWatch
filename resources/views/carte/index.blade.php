@extends('layouts.app')
@section('title', 'Carte interactive')

@push('styles')
<style>
    .content { padding: 0 !important; display: flex; flex-direction: column; height: calc(100vh - 57px); height: calc(100dvh - 57px); }
    #map { flex: 1; width: 100%; }

    /* Barre de filtres */
    .map-toolbar {
        background: #fff;
        padding: 10px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid #e8ede9;
        flex-wrap: wrap;
    }
    .map-toolbar label { font-size: 13px; font-weight: 600; color: #1a4731; }
    .map-toolbar select, .map-toolbar input {
        padding: 6px 10px;
        border: 1.5px solid #d0ddd2;
        border-radius: 6px;
        font-size: 16px;
        color: #2c3e35;
    }
    .map-toolbar select:focus, .map-toolbar input:focus { outline: none; border-color: #52b788; }
    .map-toolbar .btn { padding: 6px 14px; font-size: 13px; }

    /* Légende */
    .map-legend {
        position: absolute;
        bottom: 30px; right: 10px;
        background: rgba(255,255,255,.95);
        backdrop-filter: blur(8px);
        border-radius: 10px;
        padding: 12px 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,.12);
        z-index: 800;
        min-width: 150px;
    }
    .legend-title { font-size: 12px; font-weight: 700; color: #1a4731; margin-bottom: 8px; text-transform: uppercase; letter-spacing: .5px; }
    .legend-item  { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #2c3e35; margin-bottom: 5px; }
    .legend-dot   { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }

    /* ── Popup personnalisé ── */
    .custom-popup .leaflet-popup-content-wrapper {
        border-radius: 14px;
        box-shadow: 0 8px 30px rgba(0,0,0,.18);
        border: none;
        padding: 0;
    }
    .custom-popup .leaflet-popup-content { margin: 0; width: 290px !important; }
    .popup-header {
        background: linear-gradient(135deg, #1a4731, #2d6a4f);
        color: #fff;
        padding: 14px 16px 12px;
        border-radius: 14px 14px 0 0;
    }
    .popup-title  { font-size: 15px; font-weight: 700; }
    .popup-region { font-size: 11.5px; color: rgba(255,255,255,.7); margin-top: 2px; }
    .popup-body   { padding: 12px 16px; font-size: 13px; }
    .popup-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 5px 0; border-bottom: 1px solid #f0f3f0;
    }
    .popup-row:last-child { border-bottom: none; }
    .popup-key   { color: #6b7c72; font-size: 12.5px; }
    .popup-value { font-weight: 600; color: #1a4731; font-size: 13px; }
    .popup-badge { display: inline-block; padding: 2px 9px; border-radius: 20px; font-size: 11.5px; font-weight: 700; }

    /* Section analyse dans le popup */
    .popup-analyse-section {
        margin-top: 10px;
        background: #f0f5f1;
        border-radius: 8px;
        padding: 10px 12px;
    }
    .popup-analyse-title {
        font-size: 11px;
        font-weight: 700;
        color: #1a4731;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 6px;
    }
    .popup-analyse-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #2c3e35;
        padding: 2px 0;
    }
    .popup-analyse-row .ak { color: #6b7c72; }
    .popup-analyse-row .av { font-weight: 600; }
    .defo-bar-wrap {
        margin-top: 6px;
        background: #e8ede9;
        border-radius: 4px;
        height: 6px;
        overflow: hidden;
    }
    .defo-bar { height: 100%; border-radius: 4px; transition: width .3s; }

    .popup-no-analyse {
        font-size: 12px;
        color: #6b7c72;
        font-style: italic;
        text-align: center;
        padding: 6px 0;
    }

    .popup-actions { margin-top: 12px; display: flex; gap: 8px; }
    .popup-actions a {
        flex: 1; text-align: center; padding: 7px;
        border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none;
        transition: opacity .15s;
    }
    .popup-actions a:hover { opacity: .85; }
    .popup-btn-detail  { background: #2d6a4f; color: #fff; }
    .popup-btn-analyse { background: #f0f5f1; color: #2d6a4f; border: 1px solid #c1ddc8; }

    /* ── Mobile ── */
    @media (max-width: 768px) {
        .content { height: calc(100dvh - 57px - 64px); }
        .map-toolbar { padding: 8px 12px; gap: 8px; }
        .map-toolbar label { display: none; }
        .map-toolbar select, .map-toolbar input { padding: 6px 8px; font-size: 16px; min-width: 0; flex: 1; }
        #search-zone { min-width: 120px !important; }
        .map-legend { bottom: calc(64px + 10px); right: 10px; padding: 8px 12px; min-width: 130px; }
        .legend-title { font-size: 11px; }
        .legend-item  { font-size: 11.5px; }
    }
    @media (max-width: 480px) {
        .map-toolbar { padding: 6px 10px; }
        #filter-region { display: none; }
    }
</style>
@endpush

@section('content')
<!-- Barre de filtres -->
<div class="map-toolbar">
    <label>Filtrer :</label>
    <select id="filter-etat" onchange="filterZones()">
        <option value="">Tous les états</option>
        <option value="sain">Sain</option>
        <option value="dégradé">Dégradé</option>
        <option value="critique">Critique</option>
    </select>
    <select id="filter-region" onchange="filterZones()">
        <option value="">Toutes les régions</option>
    </select>
    <input type="text" id="search-zone" placeholder="🔍 Rechercher une zone..." oninput="filterZones()" style="min-width:200px;">
    <button class="btn btn-outline" onclick="resetFilters()">✕ Réinitialiser</button>
    <span style="margin-left:auto;font-size:13px;color:#6b7c72;" id="zones-count"></span>
</div>

<!-- Carte -->
<div id="map"></div>

<!-- Légende -->
<div class="map-legend">
    <div class="legend-title">État de la zone</div>
    <div class="legend-item"><div class="legend-dot" style="background:#52b788;"></div> Sain</div>
    <div class="legend-item"><div class="legend-dot" style="background:#f4a261;"></div> Dégradé</div>
    <div class="legend-item"><div class="legend-dot" style="background:#e63946;"></div> Critique</div>
</div>
@endsection

@push('scripts')
<script>
const COLORS = { sain: '#52b788', dégradé: '#f4a261', critique: '#e63946' };
let map, allZones = [], markers = [];

map = L.map('map').setView([10.7, -10.7], 7);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

/* ── Construction d'un marqueur avec popup lié aux analyses ── */
function makeMarker(zone) {
    const color = COLORS[zone.etat] || '#999';
    const icon = L.divIcon({
        className: '',
        html: `<div style="
            width:20px; height:20px;
            background:${color};
            border:3px solid #fff;
            border-radius:50%;
            box-shadow:0 2px 8px rgba(0,0,0,.3);
        "></div>`,
        iconSize: [20, 20],
        iconAnchor: [10, 10],
    });

    const badgeStyle = zone.etat === 'sain'
        ? 'background:#d8f3dc;color:#1a4731'
        : zone.etat === 'dégradé'
            ? 'background:#fef3cd;color:#7d5a00'
            : 'background:#fde;color:#c0392b';

    const especes = zone.especes ? zone.especes.map(function(e) { return e.nom_commun; }).join(', ') || '—' : '—';
    const nbAnalyses = zone.analyses_count || 0;

    /* ── Section analyse ── */
    let analyseHtml;
    if (zone.latest_analyse) {
        const a = zone.latest_analyse;
        const dateStr = a.date_analyse
            ? new Date(a.date_analyse).toLocaleDateString('fr-FR')
            : '—';
        const taux = a.taux_deforestation != null
            ? parseFloat(a.taux_deforestation).toFixed(1) + ' %'
            : '—';
        const tauxVal  = a.taux_deforestation != null ? parseFloat(a.taux_deforestation) : 0;
        const barColor = tauxVal < 10 ? '#52b788' : (tauxVal < 30 ? '#f4a261' : '#e63946');
        const typeLabel = a.type_analyse ? a.type_analyse.replace(/_/g, ' ') : '—';

        analyseHtml = `
            <div class="popup-analyse-section">
                <div class="popup-analyse-title">📊 Dernière analyse · ${nbAnalyses} total</div>
                <div class="popup-analyse-row">
                    <span class="ak">Type</span>
                    <span class="av">${typeLabel}</span>
                </div>
                <div class="popup-analyse-row">
                    <span class="ak">Date</span>
                    <span class="av">${dateStr}</span>
                </div>
                <div class="popup-analyse-row">
                    <span class="ak">Déforestation</span>
                    <span class="av" style="color:${barColor}">${taux}</span>
                </div>
                ${tauxVal > 0 ? `
                <div class="defo-bar-wrap">
                    <div class="defo-bar" style="width:${Math.min(tauxVal,100)}%;background:${barColor};"></div>
                </div>` : ''}
            </div>`;
    } else {
        analyseHtml = `
            <div class="popup-analyse-section">
                <div class="popup-no-analyse">
                    📭 Aucune analyse enregistrée pour cette zone
                </div>
            </div>`;
    }

    const popup = L.popup({ className: 'custom-popup', maxWidth: 310 }).setContent(`
        <div>
            <div class="popup-header">
                <div class="popup-title">🌲 ${zone.nom}</div>
                <div class="popup-region">📍 ${zone.region}</div>
            </div>
            <div class="popup-body">
                <div class="popup-row">
                    <span class="popup-key">État</span>
                    <span class="popup-badge" style="${badgeStyle}">${zone.etat}</span>
                </div>
                <div class="popup-row">
                    <span class="popup-key">Superficie</span>
                    <span class="popup-value">${zone.superficie.toLocaleString('fr')} ha</span>
                </div>
                <div class="popup-row">
                    <span class="popup-key">Espèces</span>
                    <span class="popup-value" style="font-size:11.5px;max-width:150px;text-align:right;">${especes}</span>
                </div>
                ${analyseHtml}
                <div class="popup-actions">
                    <a href="/zones/${zone.id}" class="popup-btn-detail">Détails</a>
                    <a href="/analyses?zone=${zone.id}" class="popup-btn-analyse">➕ Analyser</a>
                </div>
            </div>
        </div>
    `);

    return L.marker([zone.latitude, zone.longitude], { icon }).bindPopup(popup);
}

function renderMarkers(zones) {
    markers.forEach(function(m) { map.removeLayer(m); });
    markers = [];
    zones.forEach(function(z) {
        if (z.latitude && z.longitude) {
            const m = makeMarker(z).addTo(map);
            markers.push(m);
        }
    });
    document.getElementById('zones-count').textContent = zones.length + ' zone(s) affichée(s)';
}

function filterZones() {
    const etat   = document.getElementById('filter-etat').value;
    const region = document.getElementById('filter-region').value;
    const search = document.getElementById('search-zone').value.toLowerCase();

    const filtered = allZones.filter(function(z) {
        return (!etat   || z.etat   === etat)
            && (!region || z.region === region)
            && (!search || z.nom.toLowerCase().includes(search) || z.region.toLowerCase().includes(search));
    });
    renderMarkers(filtered);
}

function resetFilters() {
    document.getElementById('filter-etat').value   = '';
    document.getElementById('filter-region').value = '';
    document.getElementById('search-zone').value   = '';
    renderMarkers(allZones);
}

async function loadZones() {
    try {
        const res = await apiFetch('/zones');
        allZones  = await res.json();

        // Filtre région
        const regions = [...new Set(allZones.map(function(z) { return z.region; }))].sort();
        const selRegion = document.getElementById('filter-region');
        regions.forEach(function(r) {
            const opt = document.createElement('option');
            opt.value = r; opt.textContent = r;
            selRegion.appendChild(opt);
        });

        renderMarkers(allZones);

        if (allZones.length > 0 && allZones.some(function(z) { return z.latitude && z.longitude; })) {
            const bounds = allZones
                .filter(function(z) { return z.latitude && z.longitude; })
                .map(function(z) { return [z.latitude, z.longitude]; });
            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 10 });
        }
    } catch (e) { /* handled by apiFetch */ }
}

loadZones();
</script>
@endpush
