<?php
/**
 * Helpers de sesión y permisos.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkLogin(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

function checkRole(array $allowedRoles): void
{
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowedRoles, true)) {
        header('Location: unauthorized.php');
        exit();
    }
}

function isAdmin(): bool
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function isCajero(): bool
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'cajero';
}

function isInventario(): bool
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'inventario';
}

function isUsuario(): bool
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'usuario';
}

function currentUserName(): string
{
    return $_SESSION['user_name'] ?? '';
}
