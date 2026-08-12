<div class="container">
    <?php if (!$cajaActual): ?>
        <div class="card animate-fade-in">
            <div class="card-body">
                <h5 class="card-title">Apertura de Caja</h5>
                <form method="POST" class="row g-3">
                    <input type="hidden" name="action" value="abrir_caja">
                    <div class="col-md-6">
                        <label class="form-label">Monto Inicial</label>
                        <input type="number" class="form-control" name="monto_inicial" required step="0.01" min="0">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Abrir Caja</button>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="pos-container">
            <div class="product-section">
                <div class="search-container">
                    <div class="input-group mb-3">
                        <input type="text" id="producto-busqueda" class="form-control" placeholder="Escanear código de barras o buscar producto...">
                        <button class="btn btn-primary" type="button" id="buscar-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="product-list" id="resultados-busqueda"></div>
            </div>

            <div class="cart-container">
                <h4>Carrito de Compras</h4>
                <div class="cart-items" id="carrito-items"></div>
                <div class="cart-total">Total: $<span id="total">0.00</span></div>
                <div class="payment-methods">
                    <button class="btn btn-success btn-payment" type="button" onclick="procesarPago('efectivo')">
                        <i class="fas fa-money-bill-wave"></i> Efectivo
                    </button>
                    <button class="btn btn-info btn-payment" type="button" onclick="procesarPago('tarjeta')">
                        <i class="fas fa-credit-card"></i> Tarjeta
                    </button>
                </div>
                <button class="btn btn-danger" type="button" onclick="mostrarCierreCaja()">
                    <i class="fas fa-cash-register"></i> Cerrar Caja
                </button>
            </div>
        </div>

        <div class="modal fade" id="efectivo-modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pago en Efectivo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Total a Pagar</label>
                            <input type="text" class="form-control" id="total-pagar" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Monto Recibido</label>
                            <input type="number" class="form-control" id="monto-recibido" step="0.01" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cambio</label>
                            <input type="text" class="form-control" id="cambio" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="finalizarVenta('efectivo')">Finalizar Venta</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="tarjeta-modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pago con Tarjeta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Total a Pagar</label>
                            <input type="text" class="form-control" id="total-tarjeta" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Número de Autorización</label>
                            <input type="text" class="form-control" id="autorizacion-tarjeta">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="finalizarVenta('tarjeta')">Finalizar Venta</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="cierre-caja-modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cierre de Caja</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="cierre-caja-form" method="POST">
                            <input type="hidden" name="action" value="cerrar_caja">
                            <div class="mb-3">
                                <label class="form-label">Monto Inicial</label>
                                <input type="text" class="form-control" value="$<?php echo formatMoney($cajaActual['monto_inicial']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ventas del Día</label>
                                <input type="text" class="form-control" id="ventas-dia" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Monto Final en Caja</label>
                                <input type="number" class="form-control" name="monto_final" required step="0.01" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Diferencia</label>
                                <input type="text" class="form-control" id="diferencia-caja" readonly>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="cerrarCaja()">Cerrar Caja</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ticket-modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ticket de Venta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <pre id="ticket-contenido"></pre>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" onclick="imprimirTicket()">Imprimir</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
