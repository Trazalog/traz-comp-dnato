<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trazalog — Error de autorización</title>
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
        .oauth-footer { text-align: center; margin-top: 18px; font-size: 12px; color: #888; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="oauth-card">
        <h4 class="text-danger">Error de autorización</h4>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error_message) ?>
        </div>
        <p class="text-muted" style="font-size:13px;">
            Cierre esta ventana y vuelva a intentar la conexión desde el cliente.
        </p>
        <div class="oauth-footer">
            &copy; <?= date('Y') ?> Trazalog SAS. Todos los derechos reservados.
        </div>
    </div>
</div>
</body>
</html>
