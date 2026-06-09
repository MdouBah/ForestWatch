@extends('layouts.app')
@section('title', 'Gestion des comptes')

@push('styles')
    <style>
        /* ── Badges de rôle ──────────────────────────────────*/
        .role-badge {
            display: inline-block;
            padding: 3px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .rb-admin {
            background: #fde8d8;
            color: #c05621;
        }

        .rb-user {
            background: #d8f3dc;
            color: #1a4731;
        }

        .rb-visiteur {
            background: #d6eaf8;
            color: #1a5276;
        }

        /* ── Avatars ────────────────────────────────────────────────── */
        .av {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }

        .av-admin {
            background: #fde8d8;
            color: #c05621;
        }

        .av-user {
            background: #d8f3dc;
            color: #1a4731;
        }

        .av-visiteur {
            background: #d6eaf8;
            color: #1a5276;
        }

        /* ── Select inline dans le tableau ─────────────────────────── */
        .role-sel {
            padding: 4px 8px;
            border: 1.5px solid #d0ddd2;
            border-radius: 6px;
            font-size: 13px;
            color: #2c3e35;
            background: #fff;
            cursor: pointer;
        }

        .role-sel:focus {
            outline: none;
            border-color: #52b788;
        }

        /* ── Info box dans le modal ─────────────────────────────────── */
        .role-info-box {
            background: #f0f5f1;
            border: 1px solid #c1ddc8;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            color: #2d6a4f;
            line-height: 1.7;
            margin-top: 6px;
        }

        /* ── Accès refusé ───────────────────────────────────────────── */
        .denied-wrap {
            text-align: center;
            padding: 80px 20px;
        }

        .denied-wrap .denied-icon {
            font-size: 60px;
            margin-bottom: 14px;
        }
    </style>
@endpush

@section('content')

    {{-- ─── Accès refusé ────────────────────────────────────────── --}}
    <div id="div-denied" style="display:none;">
        <div class="card">
            <div class="card-body denied-wrap">
                <div class="denied-icon">🔒</div>
                <h2 style="font-size:22px;color:#1a4731;margin-bottom:8px;">Accès réservé aux administrateurs</h2>
                <p style="color:#6b7c72;font-size:14px;">Connectez-vous en tant qu'administrateur pour gérer les comptes.</p>
                <a href="/dashboard" class="btn btn-primary" style="margin-top:18px;">Retour au tableau de bord</a>
            </div>
        </div>
    </div>

    {{-- ─── Contenu principal ───────────────────────────────────── --}}
    <div id="div-main" style="display:none;">

        {{-- En-tête --}}
        <div
            style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
            <div>
                <h2 style="font-size:18px;font-weight:700;color:#1a4731;margin-bottom:4px;">👥 Gestion des utilisateurs</h2>
                <p style="font-size:13.5px;color:#6b7c72;">
                    Consultez et gérez tous les comptes. Les visiteurs s'inscrivent eux-mêmes ;
                    vous pouvez les promouvoir en <strong>agent</strong> ou <strong>administrateur</strong>.
                </p>
            </div>
            <button class="btn btn-primary" onclick="ouvrirModalCreer()" style="flex-shrink:0;">
                ➕ Nouveau compte
            </button>
        </div>

        {{-- Alerte globale --}}
        <div id="alerte" class="alert" style="margin-bottom:18px;"></div>

        {{-- Stats --}}
        <div class="stats-grid" id="stats-box" style="margin-bottom:22px;"></div>

        {{-- Tableau --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Tous les comptes</span>
                <span id="nb-total" style="font-size:13px;color:#6b7c72;"></span>
            </div>
            <div class="scroll-hint">← Faire défiler →</div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Inscrit le</th>
                            <th style="text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-tbody">
                        <tr>
                            <td colspan="5" style="text-align:center;padding:40px;color:#6b7c72;">
                                <div style="font-size:28px;margin-bottom:8px;">⏳</div>Chargement...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════
     MODAL — CRÉER UN COMPTE
══════════════════════════════════════════════════ --}}
    <div class="modal-overlay" id="modal-creer">
        <div class="modal" style="max-width:540px;">
            <div class="modal-header">
                <span class="modal-title">➕ Créer un compte</span>
                <button class="modal-close" onclick="fermerModalCreer()">×</button>
            </div>
            <div class="modal-body">

                <div id="alerte-creer" class="alert" style="margin-bottom:16px;"></div>

                <div class="grid-2col" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Prénom <span style="color:#e63946">*</span></label>
                        <input type="text" id="c-prenom" class="form-control" placeholder="Prénom">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nom <span style="color:#e63946">*</span></label>
                        <input type="text" id="c-nom" class="form-control" placeholder="Nom de famille">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Adresse email <span style="color:#e63946">*</span></label>
                    <input type="email" id="c-email" class="form-control" placeholder="email@exemple.com">
                </div>

                <div class="form-group">
                    <label class="form-label">Mot de passe <span style="color:#e63946">*</span>
                        <span style="font-weight:400;font-size:12px;color:#6b7c72;">min. 6 caractères</span>
                    </label>
                    <input type="password" id="c-password" class="form-control" placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label class="form-label">Rôle <span style="color:#e63946">*</span></label>
                    <select id="c-role" class="form-control" onchange="majInfoRoleCreer()">
                        <option value="visiteur">🔵 Visiteur — lecture seule</option>
                        <option value="user">🟢 Agent forestier — saisie d'analyses</option>
                        <option value="admin">🔴 Administrateur — accès complet</option>
                    </select>
                    <div class="role-info-box" id="info-role-creer">
                        🔵 <strong>Visiteur</strong> : accès en lecture seule à la carte, aux zones et aux espèces. Ne peut
                        pas saisir d'analyses.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="fermerModalCreer()">Annuler</button>
                <button class="btn btn-primary" id="btn-creer" onclick="creerCompte()">
                    ✅ Créer le compte
                </button>
            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════
     MODAL — MODIFIER UN COMPTE
══════════════════════════════════════════════════ --}}
    <div class="modal-overlay" id="modal-modifier">
        <div class="modal" style="max-width:540px;">
            <div class="modal-header">
                <span class="modal-title">✏️ Modifier le compte</span>
                <button class="modal-close" onclick="fermerModalModifier()">×</button>
            </div>
            <div class="modal-body">

                <div id="alerte-modifier" class="alert" style="margin-bottom:16px;"></div>
                <input type="hidden" id="m-id">

                <div class="grid-2col" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Prénom <span style="color:#e63946">*</span></label>
                        <input type="text" id="m-prenom" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nom <span style="color:#e63946">*</span></label>
                        <input type="text" id="m-nom" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email <span style="color:#e63946">*</span></label>
                    <input type="email" id="m-email" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Nouveau mot de passe
                        <span style="font-weight:400;font-size:12px;color:#6b7c72;">— laisser vide pour ne pas
                            changer</span>
                    </label>
                    <input type="password" id="m-password" class="form-control" placeholder="Laisser vide = inchangé">
                </div>

                <div class="form-group">
                    <label class="form-label">Rôle <span style="color:#e63946">*</span></label>
                    <select id="m-role" class="form-control" onchange="majInfoRoleModifier()">
                        <option value="visiteur">🔵 Visiteur</option>
                        <option value="user">🟢 Agent forestier</option>
                        <option value="admin">🔴 Administrateur</option>
                    </select>
                    <div class="role-info-box" id="info-role-modifier"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="fermerModalModifier()">Annuler</button>
                <button class="btn btn-primary" id="btn-modifier" onclick="modifierCompte()">
                    💾 Enregistrer les modifications
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        /*
                       CONSTANTES ET ÉTAT*/
        const PAGE_ROLE_INFOS = {
            visiteur: '🔵 <strong>Visiteur</strong> : lecture seule — carte, zones et espèces. Pas d\'accès aux analyses ni aux rapports. C\'est le rôle attribué à toute auto-inscription.',
            user: '🟢 <strong>Agent forestier</strong> : peut consulter toutes les données, gérer les zones et saisir des analyses. Ne peut pas gérer les comptes.',
            admin: '🔴 <strong>Administrateur</strong> : accès complet — gère les zones, espèces, analyses, rapports ET les comptes utilisateurs.',
        };

        let tousLesUsers = [];
        let monId = getUser()?.id;

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           INITIALISATION
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        document.addEventListener('DOMContentLoaded', function() {
            if (!isAdmin()) {
                document.getElementById('div-denied').style.display = 'block';
                return;
            }
            document.getElementById('div-main').style.display = 'block';
            chargerUsers();
        });

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           CHARGEMENT
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        async function chargerUsers() {
            try {
                const res = await apiFetch('/admin/users');
                if (!res.ok) {
                    const err = await res.json().catch(function() {
                        return {};
                    });
                    afficherAlerte('alerte', '❌ Impossible de charger les comptes : ' + (err.message || 'Erreur ' + res
                        .status), 'error');
                    return;
                }
                tousLesUsers = await res.json();
                afficherStats();
                afficherTableau();
            } catch (e) {
                if (e.message !== 'Non authentifié') {
                    afficherAlerte('alerte', '❌ Erreur réseau — le serveur est-il démarré ?', 'error');
                }
            }
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           STATS
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        function afficherStats() {
            const total = tousLesUsers.length;
            const admins = tousLesUsers.filter(function(u) {
                return u.role === 'admin';
            }).length;
            const agents = tousLesUsers.filter(function(u) {
                return u.role === 'user';
            }).length;
            const visits = tousLesUsers.filter(function(u) {
                return u.role === 'visiteur';
            }).length;

            document.getElementById('nb-total').textContent = total + ' compte(s)';
            document.getElementById('stats-box').innerHTML =
                '<div class="stat-card">' +
                '<div class="stat-icon">👥</div>' +
                '<div><div class="stat-value">' + total + '</div><div class="stat-label">Total</div></div>' +
                '</div>' +
                '<div class="stat-card orange">' +
                '<div class="stat-icon">🔴</div>' +
                '<div><div class="stat-value">' + admins + '</div><div class="stat-label">Admins</div></div>' +
                '</div>' +
                '<div class="stat-card">' +
                '<div class="stat-icon">🟢</div>' +
                '<div><div class="stat-value">' + agents + '</div><div class="stat-label">Agents</div></div>' +
                '</div>' +
                '<div class="stat-card bleu">' +
                '<div class="stat-icon">🔵</div>' +
                '<div><div class="stat-value">' + visits + '</div><div class="stat-label">Visiteurs</div></div>' +
                '</div>';
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           TABLEAU
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        function afficherTableau() {
            const tbody = document.getElementById('users-tbody');

            if (!tousLesUsers.length) {
                tbody.innerHTML =
                    '<tr><td colspan="5" style="text-align:center;padding:40px;color:#6b7c72;">Aucun utilisateur</td></tr>';
                return;
            }

            tbody.innerHTML = tousLesUsers.map(function(u) {
                const estMoi = (u.id === monId);
                const initiales = ((u.prenom || '').charAt(0) + (u.nom || '').charAt(0)).toUpperCase() || '?';
                const avCls = u.role === 'admin' ? 'av-admin' : (u.role === 'user' ? 'av-user' : 'av-visiteur');
                const rbCls = u.role === 'admin' ? 'rb-admin' : (u.role === 'user' ? 'rb-user' : 'rb-visiteur');
                const label = ROLE_LABELS[u.role] || u.role;
                const inscrit = new Date(u.created_at).toLocaleDateString('fr-FR');

                const colonneRole = estMoi ?
                    '<span class="role-badge ' + rbCls + '">' + label + '</span>' :
                    '<select class="role-sel" onchange="changerRole(' + u.id + ', this.value, this)">' +
                    '<option value="visiteur"' + (u.role === 'visiteur' ? ' selected' : '') +
                    '>🔵 Visiteur</option>' +
                    '<option value="user"' + (u.role === 'user' ? ' selected' : '') + '>🟢 Agent</option>' +
                    '<option value="admin"' + (u.role === 'admin' ? ' selected' : '') + '>🔴 Admin</option>' +
                    '</select>';

                const colonneActions = estMoi ?
                    '<span style="font-size:12px;color:#6b7c72;">Mon compte</span>' :
                    '<div style="display:flex;gap:6px;justify-content:center;">' +
                    '<button class="btn btn-outline btn-sm" onclick="ouvrirModalModifier(' + u.id +
                    ')" title="Modifier">✏️ Modifier</button>' +
                    '<button class="btn btn-danger btn-sm" onclick="supprimerUser(' + u.id + ',\'' + esc(u.prenom +
                        ' ' + u.nom) + '\')" title="Supprimer">🗑️</button>' +
                    '</div>';

                return '<tr>' +
                    '<td>' +
                    '<div style="display:flex;align-items:center;gap:10px;">' +
                    '<div class="av ' + avCls + '">' + initiales + '</div>' +
                    '<div>' +
                    '<div style="font-weight:600;color:#1a4731;">' + u.prenom + ' ' + u.nom + '</div>' +
                    (estMoi ? '<div style="font-size:11px;color:#6b7c72;">(votre compte)</div>' : '') +
                    '</div>' +
                    '</div>' +
                    '</td>' +
                    '<td style="color:#6b7c72;font-size:13px;">' + u.email + '</td>' +
                    '<td>' + colonneRole + '</td>' +
                    '<td style="font-size:12px;color:#6b7c72;">' + inscrit + '</td>' +
                    '<td style="text-align:center;">' + colonneActions + '</td>' +
                    '</tr>';
            }).join('');
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           CHANGEMENT DE RÔLE (inline)
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        async function changerRole(userId, nouveauRole, selectEl) {
            const ancienRole = tousLesUsers.find(function(u) {
                return u.id === userId;
            })?.role;
            try {
                const res = await apiFetch('/admin/users/' + userId + '/role', {
                    method: 'PUT',
                    body: JSON.stringify({
                        role: nouveauRole
                    }),
                });
                if (res.ok) {
                    afficherAlerte('alerte', '✅ Rôle mis à jour : ' + ROLE_LABELS[nouveauRole], 'success');
                    await chargerUsers();
                } else {
                    const err = await res.json().catch(function() {
                        return {};
                    });
                    afficherAlerte('alerte', '❌ ' + (err.message || 'Erreur lors du changement de rôle.'), 'error');
                    if (selectEl && ancienRole) selectEl.value = ancienRole; // rollback
                }
            } catch (e) {
                if (e.message !== 'Non authentifié') {
                    afficherAlerte('alerte', '❌ Erreur réseau.', 'error');
                    if (selectEl && ancienRole) selectEl.value = ancienRole;
                }
            }
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           SUPPRIMER
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        async function supprimerUser(id, nom) {
            if (!confirm('Supprimer le compte de "' + nom + '" ?\nCette action est définitive.')) return;
            try {
                const res = await apiFetch('/admin/users/' + id, {
                    method: 'DELETE'
                });
                if (res.ok) {
                    afficherAlerte('alerte', '✅ Compte de "' + nom + '" supprimé.', 'success');
                    await chargerUsers();
                } else {
                    const err = await res.json().catch(function() {
                        return {};
                    });
                    afficherAlerte('alerte', '❌ ' + (err.message || 'Erreur lors de la suppression.'), 'error');
                }
            } catch (e) {
                if (e.message !== 'Non authentifié') {
                    afficherAlerte('alerte', '❌ Erreur réseau.', 'error');
                }
            }
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           MODAL CRÉER
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        function ouvrirModalCreer() {
            ['c-prenom', 'c-nom', 'c-email', 'c-password'].forEach(function(id) {
                document.getElementById(id).value = '';
            });
            document.getElementById('c-role').value = 'visiteur';
            majInfoRoleCreer();
            masquerAlerte('alerte-creer');
            document.getElementById('modal-creer').classList.add('open');
            document.getElementById('c-prenom').focus();
        }

        function fermerModalCreer() {
            document.getElementById('modal-creer').classList.remove('open');
        }

        function majInfoRoleCreer() {
            const role = document.getElementById('c-role').value;
            document.getElementById('info-role-creer').innerHTML = PAGE_ROLE_INFOS[role] || '';
        }

        async function creerCompte() {
            const prenom = document.getElementById('c-prenom').value.trim();
            const nom = document.getElementById('c-nom').value.trim();
            const email = document.getElementById('c-email').value.trim();
            const pwd = document.getElementById('c-password').value;
            const role = document.getElementById('c-role').value;

            if (!prenom) {
                afficherAlerte('alerte-creer', '⚠️ Le prénom est obligatoire.', 'error');
                return;
            }
            if (!nom) {
                afficherAlerte('alerte-creer', '⚠️ Le nom est obligatoire.', 'error');
                return;
            }
            if (!email) {
                afficherAlerte('alerte-creer', '⚠️ L\'email est obligatoire.', 'error');
                return;
            }
            if (!pwd) {
                afficherAlerte('alerte-creer', '⚠️ Le mot de passe est obligatoire.', 'error');
                return;
            }
            if (pwd.length < 6) {
                afficherAlerte('alerte-creer', '⚠️ Minimum 6 caractères.', 'error');
                return;
            }

            const btn = document.getElementById('btn-creer');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner"></span> Création...';

            try {
                const res = await apiFetch('/admin/users', {
                    method: 'POST',
                    body: JSON.stringify({
                        prenom: prenom,
                        nom: nom,
                        email: email,
                        password: pwd,
                        role: role
                    }),
                });
                const data = await res.json();

                if (res.ok) {
                    fermerModalCreer();
                    afficherAlerte('alerte', '✅ Compte de ' + prenom + ' ' + nom + ' créé (' + (ROLE_LABELS[role] ||
                        role) + ').', 'success');
                    await chargerUsers();
                } else {
                    const msg = data.errors ?
                        Object.values(data.errors).flat().join(' · ') :
                        (data.message || 'Erreur lors de la création.');
                    afficherAlerte('alerte-creer', '❌ ' + msg, 'error');
                }
            } catch (e) {
                if (e.message !== 'Non authentifié') {
                    afficherAlerte('alerte-creer', '❌ Erreur réseau — veuillez réessayer.', 'error');
                }
            }

            btn.disabled = false;
            btn.innerHTML = '✅ Créer le compte';
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           MODAL MODIFIER
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        function ouvrirModalModifier(userId) {
            const user = tousLesUsers.find(function(u) {
                return u.id === userId;
            });
            if (!user) {
                afficherAlerte('alerte', '❌ Utilisateur introuvable.', 'error');
                return;
            }
            document.getElementById('m-id').value = user.id;
            document.getElementById('m-prenom').value = user.prenom || '';
            document.getElementById('m-nom').value = user.nom || '';
            document.getElementById('m-email').value = user.email || '';
            document.getElementById('m-password').value = '';
            document.getElementById('m-role').value = user.role;
            majInfoRoleModifier();
            masquerAlerte('alerte-modifier');
            document.getElementById('modal-modifier').classList.add('open');
            document.getElementById('m-prenom').focus();
        }

        function fermerModalModifier() {
            document.getElementById('modal-modifier').classList.remove('open');
        }

        function majInfoRoleModifier() {
            const role = document.getElementById('m-role').value;
            document.getElementById('info-role-modifier').innerHTML = PAGE_ROLE_INFOS[role] || '';
        }

        async function modifierCompte() {
            const id = document.getElementById('m-id').value;
            const prenom = document.getElementById('m-prenom').value.trim();
            const nom = document.getElementById('m-nom').value.trim();
            const email = document.getElementById('m-email').value.trim();
            const pwd = document.getElementById('m-password').value;
            const role = document.getElementById('m-role').value;

            if (!prenom || !nom || !email) {
                afficherAlerte('alerte-modifier', '⚠️ Prénom, nom et email sont obligatoires.', 'error');
                return;
            }
            if (pwd && pwd.length < 6) {
                afficherAlerte('alerte-modifier', '⚠️ Le mot de passe doit contenir au moins 6 caractères.', 'error');
                return;
            }

            const corps = {
                prenom: prenom,
                nom: nom,
                email: email,
                role: role
            };
            if (pwd) corps.password = pwd;

            const btn = document.getElementById('btn-modifier');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner"></span> Enregistrement...';

            try {
                const res = await apiFetch('/admin/users/' + id, {
                    method: 'PUT',
                    body: JSON.stringify(corps),
                });
                const data = await res.json();

                if (res.ok) {
                    fermerModalModifier();
                    afficherAlerte('alerte', '✅ Compte de ' + prenom + ' ' + nom + ' mis à jour.', 'success');
                    await chargerUsers();
                } else {
                    const msg = data.errors ?
                        Object.values(data.errors).flat().join(' · ') :
                        (data.message || 'Erreur lors de la modification.');
                    afficherAlerte('alerte-modifier', '❌ ' + msg, 'error');
                }
            } catch (e) {
                if (e.message !== 'Non authentifié') {
                    afficherAlerte('alerte-modifier', '❌ Erreur réseau.', 'error');
                }
            }

            btn.disabled = false;
            btn.innerHTML = '💾 Enregistrer les modifications';
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           UTILITAIRES
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        function afficherAlerte(elementId, message, type) {
            const el = document.getElementById(elementId);
            if (!el) return;
            el.innerHTML = message;
            el.className = 'alert alert-' + type + ' show';
            if (elementId === 'alerte') {
                setTimeout(function() {
                    el.classList.remove('show');
                }, 7000);
            }
        }

        function masquerAlerte(elementId) {
            const el = document.getElementById(elementId);
            if (el) {
                el.className = 'alert';
                el.innerHTML = '';
            }
        }

        function esc(str) {
            return (str || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
        }
    </script>
@endpush
