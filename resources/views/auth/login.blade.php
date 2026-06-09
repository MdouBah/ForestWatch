<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1a4731">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="FORESTWATCH">
    <title>FORESTWATCH — Connexion</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a4731 0%, #2d6a4f 50%, #52b788 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-container {
            display: flex;
            width: 100%;
            max-width: 900px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,.35);
            min-height: 520px;
        }
        .auth-left {
            flex: 1;
            background: rgba(255,255,255,.08);
            backdrop-filter: blur(10px);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #fff;
        }
        .auth-logo { font-size: 42px; margin-bottom: 20px; }
        .auth-brand { font-size: 28px; font-weight: 800; margin-bottom: 8px; }
        .auth-tagline { font-size: 14px; color: rgba(255,255,255,.7); line-height: 1.6; max-width: 260px; }
        .auth-features { margin-top: 36px; display: flex; flex-direction: column; gap: 14px; }
        .auth-feature { display: flex; align-items: center; gap: 10px; font-size: 13.5px; color: rgba(255,255,255,.85); }
        .auth-feature .dot { width: 8px; height: 8px; background: #52b788; border-radius: 50%; flex-shrink: 0; }

        .auth-right {
            flex: 1;
            background: #fff;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .auth-title { font-size: 22px; font-weight: 700; color: #1a4731; margin-bottom: 6px; }
        .auth-sub   { font-size: 13.5px; color: #6b7c72; margin-bottom: 30px; }

        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #2c3e35; margin-bottom: 6px; }
        .form-control {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #d0ddd2;
            border-radius: 8px;
            font-size: 16px; /* 16px évite le zoom automatique iOS */
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus { outline: none; border-color: #52b788; box-shadow: 0 0 0 3px rgba(82,183,136,.15); }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #2d6a4f;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit:hover { background: #1a4731; }
        .btn-submit:disabled { background: #a8c5b5; cursor: not-allowed; }

        .auth-link { text-align: center; margin-top: 18px; font-size: 13.5px; color: #6b7c72; }
        .auth-link a { color: #2d6a4f; font-weight: 600; text-decoration: none; }
        .auth-link a:hover { text-decoration: underline; }

        .alert { padding: 11px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; display: none; }
        .alert-error   { background: #fde; color: #c0392b; border: 1px solid #f5b7b1; display: none; }
        .alert-success { background: #d8f3dc; color: #1a4731; border: 1px solid #b7e4c7; display: none; }
        .alert.show { display: block; }

        .spinner { display: inline-block; width: 18px; height: 18px; border: 3px solid rgba(255,255,255,.3); border-top-color: #fff; border-radius: 50%; animation: spin .7s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 640px) {
            .auth-left { display: none; }
            .auth-right { padding: 36px 22px; border-radius: 20px; }
            .auth-container { border-radius: 20px; min-height: unset; box-shadow: 0 20px 50px rgba(0,0,0,.35); }
            body { padding: 16px; align-items: flex-start; padding-top: 40px; }
        }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="auth-left">
        <div class="auth-logo">🌳</div>
        <div class="auth-brand">FORESTWATCH</div>
        <div class="auth-tagline">Plateforme de surveillance, d'analyse et de gestion des ressources forestières de Guinée.</div>
        <div class="auth-features">
            <div class="auth-feature"><span class="dot"></span> Carte interactive des zones forestières</div>
            <div class="auth-feature"><span class="dot"></span> Identification des espèces d'arbres</div>
            <div class="auth-feature"><span class="dot"></span> Analyses statistiques en temps réel</div>
            <div class="auth-feature"><span class="dot"></span> Export de rapports PDF / Excel</div>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-title">Bon retour </div>
        <div class="auth-sub">Connectez-vous à votre compte FORESTWATCH</div>

        <div class="alert alert-error" id="alert-error"></div>

        <form id="login-form">
            <div class="form-group">
                <label class="form-label">Adresse email</label>
                <input type="email" id="email" class="form-control" placeholder="nom@exemple.com" required>
            </div>
            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <input type="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-submit" id="btn-login">
                Se connecter
            </button>
        </form>

        <div class="auth-link">
            Pas encore de compte ? <a href="/register">S'inscrire</a>
        </div>
    </div>
</div>

<script>
    // Vérifier que le token stocké correspond à un utilisateur valide
    (async () => {
        const token = localStorage.getItem('fw_token');
        if (!token) return;
        try {
            const res = await fetch('/api/auth/me', {
                headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' }
            });
            if (res.ok) {
                const user = await res.json();
                localStorage.setItem('fw_user', JSON.stringify(user));
                window.location.href = '/dashboard';
            } else {
                // Token invalide (utilisateur supprimé ou expiré) → nettoyer
                localStorage.clear();
            }
        } catch(e) { localStorage.clear(); }
    })();

    document.getElementById('login-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('btn-login');
        const alertEl = document.getElementById('alert-error');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Connexion...';
        alertEl.classList.remove('show');

        const res = await fetch('/api/auth/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email:    document.getElementById('email').value,
                password: document.getElementById('password').value,
            })
        });

        const data = await res.json();

        if (res.ok) {
            localStorage.setItem('fw_token', data.token);
            localStorage.setItem('fw_user',  JSON.stringify(data.user));
            window.location.href = '/dashboard';
        } else {
            alertEl.textContent = data.message || 'Identifiants incorrects.';
            alertEl.classList.add('show');
            btn.disabled = false;
            btn.textContent = 'Se connecter';
        }
    });
</script>
</body>
</html>
