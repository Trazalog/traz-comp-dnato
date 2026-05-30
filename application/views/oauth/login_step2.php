<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trazalog — Seleccione empresa</title>
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

        <h4>Seleccione su empresa</h4>
        <p class="text-muted" style="font-size:13px;">Su usuario tiene acceso a más de una empresa. Elija con cuál desea continuar.</p>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= base_url('oauth/login/select-company') ?>">
            <input type="hidden" name="oauth_csrf" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="form-group">
                <select name="empr_id" class="form-control" required>
                    <option value="">-- Seleccione empresa --</option>
                    <?php foreach ($memberships as $m): ?>
                    <option value="<?= htmlspecialchars($m['key']) ?>">
                        <?= htmlspecialchars($m['displayName']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Continuar</button>
        </form>

        <div class="oauth-footer">
            &copy; <?= date('Y') ?> Trazalog SAS. Todos los derechos reservados.
        </div>
    </div>
</div>
</body>
</html>
