<?php
/**
 * Gestión de productos (listado + CRUD por POST).
 */

require_once __DIR__ . '/includes/bootstrap.php';

checkLogin();
checkRole(['admin', 'inventario']);

$mensaje = null;
$error = null;
$productos = [];
$categorias = [];
$totalPaginas = 0;
$pagina = 1;
$busqueda = '';
$categoriaFiltro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'crear':
                $stmt = $pdo->prepare(
                    'INSERT INTO productos
                    (codigo, codigo_barras, nombre, categoria, estado, stock, stock_minimo, precio_compra, precio_venta)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([
                    $_POST['codigo'],
                    $_POST['codigo_barras'],
                    $_POST['nombre'],
                    $_POST['categoria'],
                    $_POST['estado'],
                    $_POST['stock'],
                    $_POST['stock_minimo'],
                    $_POST['precio_compra'],
                    $_POST['precio_venta']
                ]);
                $mensaje = 'Producto agregado exitosamente';
                break;

            case 'editar':
                $stmt = $pdo->prepare(
                    'UPDATE productos SET
                    codigo = ?, codigo_barras = ?, nombre = ?, categoria = ?, estado = ?,
                    stock = ?, stock_minimo = ?, precio_compra = ?, precio_venta = ?
                    WHERE id = ?'
                );
                $stmt->execute([
                    $_POST['codigo'],
                    $_POST['codigo_barras'],
                    $_POST['nombre'],
                    $_POST['categoria'],
                    $_POST['estado'],
                    $_POST['stock'],
                    $_POST['stock_minimo'],
                    $_POST['precio_compra'],
                    $_POST['precio_venta'],
                    $_POST['id']
                ]);
                $mensaje = 'Producto actualizado exitosamente';
                break;

            case 'eliminar':
                $stmt = $pdo->prepare('DELETE FROM productos WHERE id = ?');
                $stmt->execute([$_POST['id']]);
                $mensaje = 'Producto eliminado exitosamente';
                break;
        }
    } catch (PDOException $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

try {
    $stmt = $pdo->query('SELECT DISTINCT categoria FROM productos ORDER BY categoria');
    $categorias = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
    $porPagina = 10;
    $inicio = ($pagina - 1) * $porPagina;

    $busqueda = $_GET['busqueda'] ?? '';
    $categoriaFiltro = $_GET['categoria'] ?? '';

    $where = 'WHERE 1=1';
    $params = [];

    if ($busqueda !== '') {
        $where .= ' AND (nombre LIKE ? OR codigo LIKE ? OR codigo_barras LIKE ?)';
        $params = array_merge($params, ["%$busqueda%", "%$busqueda%", "%$busqueda%"]);
    }

    if ($categoriaFiltro !== '') {
        $where .= ' AND categoria = ?';
        $params[] = $categoriaFiltro;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM productos $where");
    $stmt->execute($params);
    $totalProductos = (int) $stmt->fetchColumn();
    $totalPaginas = (int) ceil($totalProductos / $porPagina);

    $sql = "SELECT * FROM productos $where ORDER BY fecha_registro DESC LIMIT $inicio, $porPagina";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Error de base de datos: ' . $e->getMessage();
}

$pageTitle = 'Gestión de Productos - Sistema de Gestión';
$activeNav = 'productos';
$navIcon = 'fa-store';
$navBrand = 'Sistema de Gestión';
$extraCss = ['productos.css'];
$extraJs = ['productos.js'];

require __DIR__ . '/views/layouts/header.php';
require __DIR__ . '/views/productos/index.php';
require __DIR__ . '/views/layouts/footer.php';
