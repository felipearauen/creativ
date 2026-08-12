<?php
/**
 * Login — valida credenciales y manda según rol.
 */

require_once __DIR__ . '/includes/bootstrap.php';

$error = null;

if (isset($_POST['login'])) {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE usuario = ? AND estado = TRUE');
    $stmt->execute([$usuario]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_role'] = $user['rol'];

        if ($user['rol'] === 'usuario') {
            redirect('pos.php');
        }

        redirect('index.php');
    }

    $error = 'Usuario o contraseña incorrectos';
}

require __DIR__ . '/views/auth/login.php';
