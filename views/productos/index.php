<div class="container">
    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-success animate-fade-in"><?php echo e($mensaje); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger animate-fade-in"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="card animate-fade-in">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" name="busqueda" placeholder="Buscar productos..." value="<?php echo e($busqueda); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="categoria" class="form-select">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo e($cat); ?>" <?php echo $categoriaFiltro === $cat ? 'selected' : ''; ?>>
                                <?php echo e($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Código de Barras</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Stock</th>
                    <th>Valor Compra</th>
                    <th>Valor Venta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr class="producto-row animate-fade-in">
                        <td><?php echo e($producto['codigo']); ?></td>
                        <td><?php echo e($producto['codigo_barras']); ?></td>
                        <td><?php echo e($producto['nombre']); ?></td>
                        <td><span class="badge bg-info"><?php echo e($producto['categoria']); ?></span></td>
                        <td>
                            <span class="badge bg-<?php echo $producto['estado'] === 'bueno' ? 'success' : ($producto['estado'] === 'regular' ? 'warning' : 'danger'); ?>">
                                <?php echo e($producto['estado']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $producto['stock'] <= $producto['stock_minimo'] ? 'danger' : 'success'; ?>">
                                <?php echo e($producto['stock']); ?>
                            </span>
                        </td>
                        <td>$<?php echo formatMoney($producto['precio_compra']); ?></td>
                        <td>$<?php echo formatMoney($producto['precio_venta']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" type="button" onclick='editarProducto(<?php echo json_encode($producto, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" type="button" onclick="eliminarProducto(<?php echo (int) $producto['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPaginas > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?php echo $i === $pagina ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($busqueda); ?>&categoria=<?php echo urlencode($categoriaFiltro); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>

    <button class="btn btn-primary btn-floating" data-bs-toggle="modal" data-bs-target="#productoModal">
        <i class="fas fa-plus"></i>
    </button>
</div>

<div class="modal fade" id="productoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gestionar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="productoForm" method="POST">
                    <input type="hidden" name="action" value="crear">
                    <input type="hidden" name="id" id="producto_id">

                    <div class="mb-3">
                        <label class="form-label">Código</label>
                        <input type="text" class="form-control" name="codigo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Código de Barras</label>
                        <input type="text" class="form-control" name="codigo_barras">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <input type="text" class="form-control" name="categoria" required list="categorias">
                        <datalist id="categorias">
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo e($cat); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado" required>
                            <option value="bueno">Bueno</option>
                            <option value="regular">Regular</option>
                            <option value="malo">Malo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" class="form-control" name="stock" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock Mínimo</label>
                        <input type="number" class="form-control" name="stock_minimo" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valor de Compra</label>
                        <input type="number" class="form-control" name="precio_compra" required min="0" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valor de Venta</label>
                        <input type="number" class="form-control" name="precio_venta" required min="0" step="0.01">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Guardar Producto</button>
                </form>
            </div>
        </div>
    </div>
</div>
