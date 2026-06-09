<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1a4731">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="FORESTWATCH">
    <title>FORESTWATCH — Inscription</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #1a4731 0%, #2d6a4f 50%, #52b788 100%);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-card {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 480px;
            padding: 44px 40px;
            box-shadow: 0 30px 80px rgba(0,0,0,.3);
        }
        .auth-logo  { text-align: center; font-size: 40px; margin-bottom: 6px; }
        .auth-brand { text-align: center; font-size: 22px; font-weight: 800; color: #1a4731; margin-bottom: 4px; }
        .auth-sub   { text-align: center; font-size: 13px; color: #6b7c72; margin-bottom: 28px; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #2c3e35; margin-bottom: 5px; }
        .form-control {
            width: 100%; padding: 11px 13px;
            border: 1.5px solid #d0ddd2; border-radius: 8px;
            font-size: 16px; /* évite zoom iOS */
            color: #2c3e35;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus { outline: none; border-color: #52b788; box-shadow: 0 0 0 3px rgba(82,183,136,.15); }

        /* Info visiteur */
        .visitor-info {
            background: #f0f5f1;
            border: 1px solid #c1ddc8;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 18px;
        }
        .visitor-info .vi-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #1a4731;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .visitor-info ul {
            margin: 0; padding-left: 18px;
            font-size: 12.5px; color: #2d6a4f; line-height: 1.8;
        }
        .visitor-info .vi-note {
            margin-top: 8px;
            font-size: 12px;
            color: #6b7c72;
            font-style: italic;
        }

        .btn-submit {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #2d6a4f, #1a4731);
            color: #fff; border: none; border-radius: 10px;
            font-size: 15px; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: opacity .2s; min-height: 48px;
        }
        .btn-submit:hover   { opacity: .9; }
        .btn-submit:active  { opacity: .8; }
        .btn-submit:disabled { opacity: .6; cursor: not-allowed; }

        .auth-link { text-align: center; margin-top: 18px; font-size: 13.5px; color: #6b7c72; }
        .auth-link a { color: #2d6a4f; font-weight: 600; text-decoration: none; }
        .auth-link a:hover { text-decoration: underline; }

        .alert { padding: 11px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; display: none; }
        .alert-error   { background: #fde; color: #c0392b; border: 1px solid #f5b7b1; display: block; }
        .alert-success { background: #d8f3dc; color: #1a4731; border: 1px solid #b7e4c7; display: block; }
        .alert.hidden  { display: none; }

        .spinner {
            display: inline-block; width: 18px; height: 18px;
            border: 3px solid rgba(255,255,255,.3); border-top-color: #fff;
            border-radius: 50%; animation: spin .7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Mobile ── */
        @media (max-width: 480px) {
            body { padding: 12px; align-items: flex-start; padding-top: 20px; }
            .auth-card { padding: 30px 20px; border-radius: 16px; }
            .form-row  { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="auth-card">
    <div class="auth-logo">🌳</div>
    <div class="auth-brand">FORESTWATCH</div>
    <div class="auth-sub">Créez votre compte pour accéder à la plateforme</div>

    <div id="alert-error"   class="alert alert-error   hidden"></div>
    <div id="alert-success" class="alert alert-success hidden"></div>

    <form id="register-form" novalidate>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" class="form-control"
                       placeholder="Votre prénom" autocomplete="given-name" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="nom">Nom</label>
                <input type="text" id="nom" name="nom" class="form-control"
                       placeholder="Votre nom" autocomplete="family-name" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Adresse email</label>
            <input type="email" id="email" name="email" class="form-control"
                   placeholder="exemple@mail.com" autocomplete="email" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Mot de passe</label>
            <input type="password" id="password" name="password" class="form-control"
                   placeholder="Minimum 6 caractères" autocomplete="new-password" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="form-control" placeholder="••••••••" autocomplete="new-password" required>
        </div>

        {{-- Info sur le rôle visiteur --}}
        <div class="visitor-info">
            <div class="vi-title">🔵 Votre compte sera créé en tant que <strong>Visiteur</strong></div>
            <ul>
                <li>Consulter la carte interactive</li>
                <li>Voir les zones forestières et les espèces</li>
                <li>Voir les cours d'eaux</li>
            </ul>
            <div class="vi-note">
                Pour accéder aux analyses et rapports, un administrateur peut vous promouvoir en <strong>Agent forestier</strong>.
            </div>
        </div>

        <button type="submit" class="btn-submit" id="btn-register">
            🌿 Créer mon compte
        </button>
    </form>

    <div class="auth-link">
        Déjà un compte ? <a href="/login">Se connecter</a>
    </div>
</div>

<script>
    /* Rediriger si déjà connecté */
    (async function () {
        const token = localStorage.getItem('fw_token');
        if (!token) return;
        try {
            const res = await fetch('/api/auth/me', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });
            if (res.ok) window.location.href = '/dashboard';
            else localStorage.clear();
        } catch (e) { localStorage.clear(); }
    })();

    document.getElementById('register-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const btn     = document.getElementById('btn-register');
        const errEl   = document.getElementById('alert-error');
        const okEl    = document.getElementById('alert-success');
        const prenom  = document.getElementById('prenom').value.trim();
        const nom     = document.getElementById('nom').value.trim();
        const email   = document.getElementById('email').value.trim();
        const pwd     = document.getElementById('password').value;
        const pwdConf = document.getElementById('password_confirmation').value;

        errEl.classList.add('hidden');
        okEl.classList.add('hidden');

        /* Validation côté client */
        if (!prenom || !nom)   { showError('Le prénom et le nom sont obligatoires.'); return; }
        if (!email)            { showError('L\'adresse email est obligatoire.'); return; }
        if (pwd.length < 6)    { showError('Le mot de passe doit contenir au moins 6 caractères.'); return; }
        if (pwd !== pwdConf)   { showError('Les deux mots de passe ne correspondent pas.'); return; }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Inscription en cours...';

        try {
            const res = await fetch('/api/auth/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json'
                },
                body: JSON.stringify({
                    nom:                   nom,
                    prenom:                prenom,
                    email:                 email,
                    password:              pwd,
                    password_confirmation: pwdConf
                })
            });

            const data = await res.json();

            if (res.ok) {
                localStorage.setItem('fw_token', data.token);
                localStorage.setItem('fw_user',  JSON.stringify(data.user));
                okEl.textContent = '✅ Compte créé avec succès ! Redirection...';
                okEl.classList.remove('hidden');
                setTimeout(function () { window.location.href = '/dashboard'; }, 1000);
            } else {
                const msg = data.errors
                    ? Object.values(data.errors).flat().join(' | ')
                    : (data.message || 'Erreur lors de l\'inscription.');
                showError(msg);
                btn.disabled = false;
                btn.innerHTML = '🌿 Créer mon compte';
            }
        } catch (networkErr) {
            showError('Erreur réseau — vérifiez votre connexion.');
            btn.disabled = false;
            btn.innerHTML = '🌿 Créer mon compte';
        }
    });

    function showError(msg) {
        const el = document.getElementById('alert-error');
        el.textContent = '⚠️ ' + msg;
        el.classList.remove('hidden');
    }
</script>
</body>
</html>
