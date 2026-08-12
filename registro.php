<?php
/**
 * Alta de usuarios con rol "usuario" (acceso a POS).
 */

require_once __DIR__ . '/includes/bootstrap.php';

$error = null;
$mensaje = null;

if (isset($_POST['registro'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmarPassword = $_POST['confirmar_password'] ?? '';

    try {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE usuario = ?');
        $stmt->execute([$usuario]);

        if ($stmt->rowCount() > 0) {
            $error = 'El nombre de usuario ya está en uso';
        } elseif ($password !== $confirmarPassword) {
            $error = 'Las contraseñas no coinciden';
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO usuarios (nombre, usuario, password, rol) VALUES (?, ?, ?, 'usuario')"
            );
            $stmt->execute([$nombre, $usuario, password_hash($password, PASSWORD_DEFAULT)]);

            $mensaje = 'Usuario registrado exitosamente. Por favor, inicia sesión.';
            header('refresh:2;url=login.php');
        }
    } catch (PDOException $e) {
        $error = 'Error al registrar el usuario: ' . $e->getMessage();
    }
}

require __DIR__ . '/views/auth/registro.php';
