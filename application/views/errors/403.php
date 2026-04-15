<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso Denegado</title>
    <link href="<?= base_url() ?>vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .error-box { text-align: center; padding: 50px 40px; background: #fff; border-radius: 8px; box-shadow: 0 2px 16px rgba(0,0,0,0.08); max-width: 460px; }
        .error-code { font-size: 80px; font-weight: 700; color: #dc3545; line-height: 1; }
        .error-title { font-size: 24px; font-weight: 600; color: #343a40; margin: 12px 0 8px; }
        .error-msg { color: #6c757d; margin-bottom: 28px; }
    </style>
</head>
<body>
    <div class="error-box">
        <div class="error-code">403</div>
        <div class="error-title">Acceso Denegado</div>
        <p class="error-msg">No tienes permisos para acceder a esta sección.<br>Contacta al administrador si crees que es un error.</p>
        <a href="<?= base_url('welcome/home') ?>" class="btn btn-primary">
            Volver al inicio
        </a>
    </div>
</body>
</html>
