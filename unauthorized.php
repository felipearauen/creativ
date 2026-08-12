<?php
/**
 * Página de acceso denegado.
 */

require_once __DIR__ . '/includes/bootstrap.php';
checkLogin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No autorizado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="text-center p-4">
        <h1 class="mb-3">403</h1>
        <p class="mb-4">No tenés permiso para ver esta sección.</p>
        <a class="btn btn-primary" href="<?php echo isAdmin() ? 'index.php' : 'pos.php'; ?>">Volver</a>
    </div>
</body>
</html>
