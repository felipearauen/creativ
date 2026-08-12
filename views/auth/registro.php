<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <div class="auth-container auth-wide">
        <div class="auth-header">
            <i class="fas fa-user-plus"></i>
            <h2>Registro de Usuario</h2>
            <p class="text-muted">Crea tu cuenta para acceder al sistema</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="mensaje mensaje-error"><?php echo e($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($mensaje)): ?>
            <div class="mensaje mensaje-exito"><?php echo e($mensaje); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Nombre Completo</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" name="usuario" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirmar Contraseña</label>
                <input type="password" name="confirmar_password" class="form-control" required>
            </div>
            <button type="submit" name="registro" class="btn btn-auth btn-primary">
                Registrarse
            </button>
        </form>

        <div class="login-link">
            <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar Sesión</a></p>
        </div>
    </div>
</body>
</html>
