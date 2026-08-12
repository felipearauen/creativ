<?php
$pageTitle = $pageTitle ?? 'Sistema de Gestión';
$activeNav = $activeNav ?? '';
$extraCss = $extraCss ?? [];
$extraHeadScripts = $extraHeadScripts ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <?php foreach ($extraCss as $cssFile): ?>
        <link rel="stylesheet" href="assets/css/<?php echo e($cssFile); ?>">
    <?php endforeach; ?>
    <?php foreach ($extraHeadScripts as $scriptSrc): ?>
        <script src="<?php echo e($scriptSrc); ?>"></script>
    <?php endforeach; ?>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas <?php echo e($navIcon ?? 'fa-store'); ?> me-2"></i>
            <?php echo e($navBrand ?? 'Sistema de Gestión'); ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if (isAdmin() || isInventario()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activeNav === 'productos' ? 'active' : ''; ?>" href="index.php">Productos</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $activeNav === 'pos' ? 'active' : ''; ?>" href="pos.php">Punto de Venta</a>
                </li>
                <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activeNav === 'reportes' ? 'active' : ''; ?>" href="reportes.php">Reportes</a>
                    </li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text">
                Bienvenido, <?php echo e(currentUserName()); ?>
                <a href="logout.php" class="btn btn-outline-light ms-3">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </a>
            </span>
        </div>
    </div>
</nav>
