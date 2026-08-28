<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trazalog — Autorización OAuth</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        body { background-color: #f5f5f5; }
        .oauth-card {
            max-width: 420px;
            margin: 60px auto 0;
            background: #fff;
            border-radius: 6px;
            padding: 32px 36px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.12);
        }
        .oauth-card img.logo { max-width: 220px; height: auto; margin-bottom: 20px; }
        .consent-banner {
            background: #f0f4ff;
            border-left: 4px solid #337ab7;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #333;
        }
        .oauth-card .btn-primary { width: 100%; margin-top: 8px; }
        .oauth-footer { text-align: center; margin-top: 18px; font-size: 12px; color: #888; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="oauth-card">

        <?php if (!empty($logo_empresa)): ?>
        <div class="text-center">
            <img class="logo" src="<?= htmlspecialchars($logo_empresa) ?>" alt="Trazalog">
        </div>
        <?php endif; ?>

        <div class="consent-banner">
            <strong><?= htmlspecialchars($client_name) ?></strong> solicita acceder a <strong>Trazalog</strong>.<br>
            Ingrese sus credenciales para continuar.
        </div>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= base_url('oauth/login/credentials') ?>">
            <input type="hidden" name="oauth_csrf" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="form-group">
                <input type="email" name="email" class="form-control"
                       placeholder="Correo electrónico" required autofocus
                       value="<?= htmlspecialchars($this->input->post('email') ?: '') ?>">
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control"
                       placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Ingresar</button>
        </form>

        <div class="oauth-footer">
            &copy; <?= date('Y') ?> Trazalog SAS. Todos los derechos reservados.
        </div>
    </div>
</div>
</body>
</html>
