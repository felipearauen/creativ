<?php
/**
 * Punto de venta: apertura/cierre de caja + UI de cobro.
 * Las acciones AJAX viven en api/pos_actions.php
 */

require_once __DIR__ . '/includes/bootstrap.php';

checkLogin();
checkRole(['cajero', 'admin', 'usuario']);

$mensaje = null;
$error = null;

$horaActual = date('H:i:s');
$turnoQuery = $pdo->prepare(
    'SELECT * FROM turnos WHERE hora_inicio <= ? AND hora_fin >= ? AND dia_semana = ?'
);
$turnoQuery->execute([$horaActual, $horaActual, date('N')]);
$turnoActual = $turnoQuery->fetch();

$cajaQuery = $pdo->prepare(
    "SELECT * FROM cajas WHERE usuario_id = ? AND fecha = CURRENT_DATE() AND estado = 'abierta'"
);
$cajaQuery->execute([$_SESSION['user_id']]);
$cajaActual = $cajaQuery->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'abrir_caja':
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO cajas (usuario_id, monto_inicial, fecha, hora_apertura, estado)
                     VALUES (?, ?, CURRENT_DATE(), CURRENT_TIME(), 'abierta')"
                );
                $stmt->execute([$_SESSION['user_id'], $_POST['monto_inicial']]);
                redirect('pos.php');
            } catch (PDOException $e) {
                $error = 'Error al abrir la caja: ' . $e->getMessage();
            }
            break;

        case 'cerrar_caja':
            try {
                if (!$cajaActual) {
                    throw new RuntimeException('No hay caja abierta');
                }

                $stmt = $pdo->prepare(
                    "UPDATE cajas SET estado = 'cerrada', monto_final = ?, hora_cierre = CURRENT_TIME() WHERE id = ?"
                );
                $stmt->execute([$_POST['monto_final'], $cajaActual['id']]);
                redirect('pos.php');
            } catch (Exception $e) {
                $error = 'Error al cerrar la caja: ' . $e->getMessage();
            }
            break;
    }
}

$pageTitle = 'Punto de Venta - Sistema de Gestión';
$activeNav = 'pos';
$navIcon = 'fa-cash-register';
$navBrand = 'Punto de Venta';
$extraCss = ['pos.css'];
$extraJs = ['pos.js'];
$inlineData = 'window.POS_CONFIG = ' . json_encode([
    'userName' => currentUserName(),
    'montoInicial' => $cajaActual['monto_inicial'] ?? 0,
], JSON_UNESCAPED_UNICODE) . ';';

require __DIR__ . '/views/layouts/header.php';
require __DIR__ . '/views/pos/index.php';
require __DIR__ . '/views/layouts/footer.php';
