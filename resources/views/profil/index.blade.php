@extends('layouts.app')
@section('title', 'Mon profil')

@push('styles')
<style>
/* ── Layout ─────────────────────────────────────────────────── */
.profil-grid {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 24px;
    max-width: 960px;
    margin: 0 auto;
}
@media (max-width: 720px) {
    .profil-grid { grid-template-columns: 1fr; }
}

/* ── Carte identité ─────────────────────────────────────────── */
.identity-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 32px 20px 24px;
    text-align: center;
}

/* ── Avatar + bouton photo ──────────────────────────────────── */
.avatar-wrap {
    position: relative;
    width: 100px;
    height: 100px;
    margin-bottom: 16px;
    cursor: pointer;
}
.big-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2d6a4f, #52b788);
    color: #fff;
    font-size: 34px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border: 3px solid #fff;
    box-shadow: 0 4px 16px rgba(0,0,0,.15);
}
.big-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}
.avatar-overlay {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: rgba(0,0,0,.45);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity .2s;
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    gap: 3px;
}
.avatar-wrap:hover .avatar-overlay { opacity: 1; }
.avatar-overlay .cam { font-size: 22px; }
.avatar-upload-hint {
    font-size: 11.5px;
    color: #6b7c72;
    margin-top: 4px;
}

/* ── Infos identité ─────────────────────────────────────────── */
.avatar-name  { font-size: 18px; font-weight: 700; color: #1a4731; margin-bottom: 4px; }
.avatar-email { font-size: 13px; color: #6b7c72; margin-bottom: 14px; word-break: break-all; }

.role-pill { padding: 5px 14px; border-radius: 20px; font-size: 12.5px; font-weight: 700; }
.role-admin    { background:#fde8d8; color:#c05621; }
.role-user     { background:#d8f3dc; color:#1a4731; }
.role-visiteur { background:#d6eaf8; color:#1a5276; }

.droits-box { margin-top: 24px; width: 100%; text-align: left; }
.droits-title {
    font-size: 12px; font-weight: 700; color: #1a4731;
    text-transform: uppercase; letter-spacing: .5px;
    margin-bottom: 10px; padding-bottom: 6px;
    border-bottom: 2px solid #eef1ee;
}

/* ── Spinner photo ──────────────────────────────────────────── */
.photo-loading {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: rgba(255,255,255,.7);
    display: none;
    align-items: center;
    justify-content: center;
}
.photo-loading.active { display: flex; }

/* ── Bouton supprimer photo ─────────────────────────────────── */
#btn-del-photo {
    display: none;
    margin-top: 8px;
    font-size: 12px;
    color: #c0392b;
    background: none;
    border: none;
    cursor: pointer;
    text-decoration: underline;
    padding: 0;
}
</style>
@endpush

@section('content')
<div class="profil-grid">

    {{-- ── Carte identité ─────────────────────────────── --}}
    <div class="card identity-card">

        {{-- Avatar cliquable --}}
        <div class="avatar-wrap" onclick="document.getElementById('photo-input').click()" title="Changer la photo">
            <div class="big-avatar" id="p-avatar">
                <span id="p-initiales">??</span>
            </div>
            <div class="avatar-overlay">
                <span class="cam">📷</span>
                <span>Changer</span>
            </div>
            <div class="photo-loading" id="photo-loading">
                <span class="spinner" style="border-color:rgba(0,0,0,.15);border-top-color:#2d6a4f;width:28px;height:28px;"></span>
            </div>
        </div>

        {{-- Input caché pour sélection fichier --}}
        <input type="file" id="photo-input" accept="image/jpeg,image/png,image/webp"
               style="display:none;" onchange="uploadPhoto(this)">

        <div class="avatar-upload-hint">JPG, PNG, WebP · max 2 Mo</div>
        <button id="btn-del-photo" onclick="supprimerPhoto()">🗑 Supprimer la photo</button>

        <div id="alert-photo" class="alert" style="margin-top:10px;width:100%;"></div>

        <div class="avatar-name"  id="p-fullname">—</div>
        <div class="avatar-email" id="p-email">—</div>
        <span class="role-pill" id="p-role-pill">—</span>

        <div class="droits-box">
            <div class="droits-title">Droits d'accès</div>
            <div id="p-droits" style="font-size:13px;line-height:2.1;color:#2c3e35;"></div>
        </div>
    </div>

    {{-- ── Formulaires ─────────────────────────────────── --}}
    <div style="display:flex;flex-direction:column;gap:22px;">

        {{-- Infos personnelles --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">✏️ Informations personnelles</span>
            </div>
            <div class="card-body">
                <div id="alert-info" class="alert"></div>
                <div class="grid-2col" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Prénom</label>
                        <input type="text" id="f-prenom" class="form-control" placeholder="Votre prénom">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nom</label>
                        <input type="text" id="f-nom" class="form-control" placeholder="Votre nom">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Adresse email</label>
                    <input type="email" id="f-email" class="form-control" placeholder="votre@email.com">
                </div>
                <button class="btn btn-primary" id="btn-info" onclick="saveInfo()">
                    💾 Enregistrer les modifications
                </button>
            </div>
        </div>

        {{-- Mot de passe --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">🔒 Changer le mot de passe</span>
            </div>
            <div class="card-body">
                <div id="alert-pwd" class="alert"></div>
                <div class="form-group">
                    <label class="form-label">Nouveau mot de passe
                        <span style="font-weight:400;font-size:12px;color:#6b7c72;">— min. 6 caractères</span>
                    </label>
                    <input type="password" id="f-password" class="form-control" placeholder="••••••••">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="f-password-confirm" class="form-control" placeholder="••••••••">
                </div>
                <button class="btn btn-primary" id="btn-pwd" onclick="savePassword()">
                    🔑 Changer le mot de passe
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ────────────────────────────────────────────────
   CONSTANTES (ROLE_LABELS déjà dans le layout)
──────────────────────────────────────────────── */
const ROLE_PILLS = { admin:'role-admin', user:'role-user', visiteur:'role-visiteur' };
const DROITS = {
    admin:    '✅ Tableau de bord<br>✅ Carte & zones<br>✅ Espèces / Cours-eaux<br>✅ Analyses forestières<br>✅ Rapports PDF<br>✅ Gestion des comptes',
    user:     '✅ Tableau de bord<br>✅ Carte & zones<br>✅ Espèces / Cours-eaux<br>✅ Analyses forestières<br>✅ Rapports PDF<br>❌ Gestion des comptes',
    visiteur: '✅ Tableau de bord<br>✅ Carte & zones<br>✅ Espèces / Cours-eaux<br>❌ Analyses forestières<br>❌ Rapports PDF<br>❌ Gestion des comptes',
};

/* ────────────────────────────────────────────────
   CHARGEMENT DU PROFIL
──────────────────────────────────────────────── */
async function loadProfil() {
    try {
        const res  = await apiFetch('/auth/me');
        const user = await res.json();
        afficherProfil(user);
    } catch (e) { /* auth handled by apiFetch */ }
}

function afficherProfil(user) {
    const initiales = ((user.prenom || '').charAt(0) + (user.nom || '').charAt(0)).toUpperCase() || '?';

    // Avatar : photo ou initiales
    const avatarEl = document.getElementById('p-avatar');
    if (user.photo) {
        avatarEl.innerHTML = '<img src="/storage/avatars/' + user.photo + '" alt="Photo de profil" id="p-photo-img">';
        document.getElementById('btn-del-photo').style.display = 'inline';
    } else {
        avatarEl.innerHTML = '<span id="p-initiales">' + initiales + '</span>';
        document.getElementById('btn-del-photo').style.display = 'none';
    }

    document.getElementById('p-fullname').textContent = user.prenom + ' ' + user.nom;
    document.getElementById('p-email').textContent    = user.email;

    const pill = document.getElementById('p-role-pill');
    pill.textContent = ROLE_LABELS[user.role] || user.role;
    pill.className   = 'role-pill ' + (ROLE_PILLS[user.role] || '');

    document.getElementById('p-droits').innerHTML = DROITS[user.role] || '—';

    document.getElementById('f-prenom').value = user.prenom || '';
    document.getElementById('f-nom').value    = user.nom    || '';
    document.getElementById('f-email').value  = user.email  || '';
}

/* ────────────────────────────────────────────────
   UPLOAD PHOTO
──────────────────────────────────────────────── */
async function uploadPhoto(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];

    if (file.size > 2 * 1024 * 1024) {
        showAlert('alert-photo', '⚠️ La photo ne doit pas dépasser 2 Mo.', 'error');
        input.value = '';
        return;
    }

    // Aperçu immédiat
    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('p-avatar').innerHTML =
            '<img src="' + e.target.result + '" alt="Aperçu" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">';
    };
    reader.readAsDataURL(file);

    document.getElementById('photo-loading').classList.add('active');

    const formData = new FormData();
    formData.append('photo', file);

    try {
        const res = await fetch('/api/auth/profile/photo', {
            method:  'POST',
            headers: {
                'Authorization': 'Bearer ' + getToken(),
                'Accept':        'application/json',
                // PAS de Content-Type ici — le navigateur le définit automatiquement avec le boundary
            },
            body: formData,
        });

        const data = await res.json();

        if (res.ok) {
            // Mettre à jour localStorage
            const stored = JSON.parse(localStorage.getItem('fw_user') || '{}');
            localStorage.setItem('fw_user', JSON.stringify({ ...stored, ...data.user }));
            afficherProfil(data.user);
            showAlert('alert-photo', '✅ Photo mise à jour avec succès !', 'success');
        } else {
            showAlert('alert-photo', '❌ ' + (data.message || 'Erreur lors de l\'envoi.'), 'error');
            loadProfil(); // Rétablir l'ancienne photo
        }
    } catch (e) {
        showAlert('alert-photo', '❌ Erreur réseau.', 'error');
        loadProfil();
    }

    document.getElementById('photo-loading').classList.remove('active');
    input.value = '';
}

/* ────────────────────────────────────────────────
   SUPPRIMER PHOTO
──────────────────────────────────────────────── */
async function supprimerPhoto() {
    if (!confirm('Supprimer votre photo de profil ?')) return;
    try {
        const res  = await apiFetch('/auth/profile/photo/delete', { method: 'POST' });
        const data = await res.json();
        if (res.ok) {
            const stored = JSON.parse(localStorage.getItem('fw_user') || '{}');
            localStorage.setItem('fw_user', JSON.stringify({ ...stored, photo: null }));
            afficherProfil(data.user);
            showAlert('alert-photo', '✅ Photo supprimée.', 'success');
        } else {
            showAlert('alert-photo', '❌ ' + (data.message || 'Erreur.'), 'error');
        }
    } catch (e) { /* handled */ }
}

/* ────────────────────────────────────────────────
   MODIFIER INFOS PERSONNELLES
──────────────────────────────────────────────── */
async function saveInfo() {
    const btn = document.getElementById('btn-info');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Enregistrement...';

    try {
        const res = await apiFetch('/auth/profile', {
            method: 'PUT',
            body: JSON.stringify({
                prenom: document.getElementById('f-prenom').value.trim(),
                nom:    document.getElementById('f-nom').value.trim(),
                email:  document.getElementById('f-email').value.trim(),
            }),
        });
        const data = await res.json();

        if (res.ok) {
            const stored = JSON.parse(localStorage.getItem('fw_user') || '{}');
            localStorage.setItem('fw_user', JSON.stringify({ ...stored, ...data.user }));
            afficherProfil(data.user);
            showAlert('alert-info', '✅ Profil mis à jour avec succès !', 'success');
        } else {
            const msg = data.errors
                ? Object.values(data.errors).flat().join(' | ')
                : (data.message || 'Erreur.');
            showAlert('alert-info', '❌ ' + msg, 'error');
        }
    } catch (e) { /* handled */ }

    btn.disabled = false;
    btn.innerHTML = '💾 Enregistrer les modifications';
}

/* ────────────────────────────────────────────────
   CHANGER MOT DE PASSE
──────────────────────────────────────────────── */
async function savePassword() {
    const pwd     = document.getElementById('f-password').value;
    const confirm = document.getElementById('f-password-confirm').value;

    if (!pwd)             { showAlert('alert-pwd', '⚠️ Veuillez saisir un nouveau mot de passe.', 'error'); return; }
    if (pwd.length < 6)   { showAlert('alert-pwd', '⚠️ Minimum 6 caractères.', 'error'); return; }
    if (pwd !== confirm)  { showAlert('alert-pwd', '⚠️ Les mots de passe ne correspondent pas.', 'error'); return; }

    const btn = document.getElementById('btn-pwd');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Modification...';

    try {
        const res = await apiFetch('/auth/profile', {
            method: 'PUT',
            body: JSON.stringify({ password: pwd, password_confirmation: confirm }),
        });
        const data = await res.json();

        if (res.ok) {
            showAlert('alert-pwd', '✅ Mot de passe modifié avec succès !', 'success');
            document.getElementById('f-password').value         = '';
            document.getElementById('f-password-confirm').value = '';
        } else {
            const msg = data.errors
                ? Object.values(data.errors).flat().join(' | ')
                : (data.message || 'Erreur.');
            showAlert('alert-pwd', '❌ ' + msg, 'error');
        }
    } catch (e) { /* handled */ }

    btn.disabled = false;
    btn.innerHTML = '🔑 Changer le mot de passe';
}

/* ────────────────────────────────────────────────
   UTILITAIRE
──────────────────────────────────────────────── */
function showAlert(id, msg, type) {
    const el = document.getElementById(id);
    if (!el) return;
    el.innerHTML  = msg;
    el.className  = 'alert alert-' + type + ' show';
    setTimeout(function () { el.classList.remove('show'); }, 5000);
}

loadProfil();
</script>
@endpush
