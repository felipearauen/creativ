<?php
/**
 * Conexión PDO para entorno local (XAMPP).
 * Si migrás a hosting, cambiá host/user/pass acá.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'tienda_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

$pdo = null;

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(
        'Error de conexión: ' . $e->getMessage() .
        ". Verificá que MySQL esté activo y que exista la base '" . DB_NAME . "'."
    );
}
