/**
 * POS: carrito, cobro y cierre de caja.
 * Espera POS_CONFIG inyectado desde PHP (usuario, monto inicial, etc).
 */

document.addEventListener('DOMContentLoaded', () => {
    const config = window.POS_CONFIG || {};
    const busquedaInput = document.getElementById('producto-busqueda');

    // Si todavía no abrió caja, no hay UI de venta
    if (!busquedaInput) {
        return;
    }

    let carrito = [];

    const efectivoModal = new bootstrap.Modal(document.getElementById('efectivo-modal'));
    const tarjetaModal = new bootstrap.Modal(document.getElementById('tarjeta-modal'));
    const cierreCajaModal = new bootstrap.Modal(document.getElementById('cierre-caja-modal'));
    const ticketModal = new bootstrap.Modal(document.getElementById('ticket-modal'));

    busquedaInput.addEventListener('keyup', (e) => {
        if (e.key === 'Enter') {
            buscarProducto();
        }
    });

    document.getElementById('buscar-btn').addEventListener('click', buscarProducto);

    function buscarProducto() {
        const codigo = busquedaInput.value.trim();
        if (!codigo) {
            return;
        }

        fetch('api/pos_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=buscar_producto&codigo=${encodeURIComponent(codigo)}`
        })
            .then((response) => response.json())
            .then((producto) => {
                if (producto && producto.id) {
                    mostrarResultadoBusqueda(producto);
                } else {
                    alert('Producto no encontrado');
                }
                busquedaInput.value = '';
            })
            .catch((error) => console.error('Error al buscar:', error));
    }

    function mostrarResultadoBusqueda(producto) {
        const card = document.createElement('div');
        card.className = 'card mb-2 product-item animate-fade-in';
        card.innerHTML = `
            <div class="card-body">
                <h5 class="card-title">${producto.nombre}</h5>
                <p class="card-text">
                    Código: ${producto.codigo}<br>
                    Precio: $${producto.precio_venta}
                </p>
                <button class="btn btn-primary btn-sm" type="button">Agregar al Carrito</button>
            </div>
        `;
        card.querySelector('button').addEventListener('click', () => agregarAlCarrito(producto));
        document.getElementById('resultados-busqueda').prepend(card);
    }

    function agregarAlCarrito(producto) {
        const itemExistente = carrito.find((item) => item.id === producto.id);

        if (itemExistente) {
            itemExistente.cantidad += 1;
        } else {
            carrito.push({
                id: producto.id,
                nombre: producto.nombre,
                precio: parseFloat(producto.precio_venta),
                cantidad: 1
            });
        }

        actualizarCarritoUI();
    }

    function actualizarCarritoUI() {
        const carritoItems = document.getElementById('carrito-items');
        carritoItems.innerHTML = '';

        let total = 0;

        carrito.forEach((item, index) => {
            const subtotal = item.precio * item.cantidad;
            total += subtotal;

            const div = document.createElement('div');
            div.className = 'card mb-2 animate-fade-in';
            div.innerHTML = `
                <div class="card-body">
                    <h6 class="card-title">${item.nombre}</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="input-group" style="width: 120px;">
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-action="menos">-</button>
                            <input type="number" class="form-control form-control-sm" value="${item.cantidad}">
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-action="mas">+</button>
                        </div>
                        <div>
                            <span class="me-2">$${subtotal.toFixed(2)}</span>
                            <button class="btn btn-danger btn-sm" type="button" data-action="eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            div.querySelector('[data-action="menos"]').addEventListener('click', () => actualizarCantidad(index, -1));
            div.querySelector('[data-action="mas"]').addEventListener('click', () => actualizarCantidad(index, 1));
            div.querySelector('[data-action="eliminar"]').addEventListener('click', () => eliminarDelCarrito(index));
            div.querySelector('input').addEventListener('change', (e) => {
                actualizarCantidadDirecta(index, e.target.value);
            });

            carritoItems.appendChild(div);
        });

        document.getElementById('total').textContent = total.toFixed(2);
    }

    function actualizarCantidad(index, cambio) {
        carrito[index].cantidad = Math.max(1, carrito[index].cantidad + cambio);
        actualizarCarritoUI();
    }

    function actualizarCantidadDirecta(index, nuevaCantidad) {
        carrito[index].cantidad = Math.max(1, parseInt(nuevaCantidad, 10) || 1);
        actualizarCarritoUI();
    }

    function eliminarDelCarrito(index) {
        carrito.splice(index, 1);
        actualizarCarritoUI();
    }

    window.procesarPago = function procesarPago(metodo) {
        const total = parseFloat(document.getElementById('total').textContent);

        if (carrito.length === 0) {
            alert('El carrito está vacío');
            return;
        }

        if (metodo === 'efectivo') {
            document.getElementById('total-pagar').value = total.toFixed(2);
            document.getElementById('monto-recibido').value = '';
            document.getElementById('cambio').value = '';
            efectivoModal.show();
        } else {
            document.getElementById('total-tarjeta').value = total.toFixed(2);
            document.getElementById('autorizacion-tarjeta').value = '';
            tarjetaModal.show();
        }
    };

    document.getElementById('monto-recibido').addEventListener('input', function () {
        const total = parseFloat(document.getElementById('total-pagar').value);
        const recibido = parseFloat(this.value) || 0;
        const cambio = recibido - total;
        document.getElementById('cambio').value = cambio >= 0 ? cambio.toFixed(2) : 'Monto insuficiente';
    });

    window.finalizarVenta = function finalizarVenta(metodoPago) {
        const total = parseFloat(document.getElementById('total').textContent);
        const snapshotCarrito = carrito.map((item) => ({ ...item }));

        if (metodoPago === 'efectivo') {
            const recibido = parseFloat(document.getElementById('monto-recibido').value);
            if (recibido < total) {
                alert('El monto recibido es insuficiente');
                return;
            }
        }

        fetch('api/pos_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=procesar_venta&total=${total}&metodo_pago=${metodoPago}&productos=${encodeURIComponent(JSON.stringify(carrito))}`
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    efectivoModal.hide();
                    tarjetaModal.hide();
                    mostrarTicket(data.venta_id, snapshotCarrito, total);
                    carrito = [];
                    actualizarCarritoUI();
                } else {
                    alert('Error al procesar la venta: ' + (data.error || 'desconocido'));
                }
            })
            .catch((error) => console.error('Error al vender:', error));
    };

    function mostrarTicket(ventaId, items, total) {
        const fecha = new Date().toLocaleString();
        let ticket = `
                SUPERMERCADO EJEMPLO
                ------------------
                Fecha: ${fecha}
                Cajero: ${config.userName || ''}
                Venta #${ventaId}
                ------------------
                PRODUCTOS:
            `;

        items.forEach((item) => {
            ticket += `
                ${item.nombre}
                ${item.cantidad} x $${item.precio} = $${(item.cantidad * item.precio).toFixed(2)}
                `;
        });

        ticket += `
                ------------------
                TOTAL: $${Number(total).toFixed(2)}
                ------------------
                ¡Gracias por su compra!
            `;

        document.getElementById('ticket-contenido').textContent = ticket;
        ticketModal.show();
    }

    window.imprimirTicket = function imprimirTicket() {
        const contenido = document.getElementById('ticket-contenido').textContent;
        const ventana = window.open('', 'PRINT', 'height=600,width=800');

        ventana.document.write(`
            <html>
            <head>
                <title>Ticket de Venta</title>
                <style>body { font-family: monospace; white-space: pre; }</style>
            </head>
            <body>${contenido}</body>
            </html>
        `);

        ventana.document.close();
        ventana.focus();
        ventana.print();
        ventana.close();
    };

    window.mostrarCierreCaja = function mostrarCierreCaja() {
        // TODO: traer ventas del día por AJAX cuando haya endpoint
        document.getElementById('ventas-dia').value = '$0.00';
        cierreCajaModal.show();
    };

    window.cerrarCaja = function cerrarCaja() {
        document.getElementById('cierre-caja-form').submit();
    };

    const montoFinalInput = document.querySelector('input[name="monto_final"]');
    if (montoFinalInput) {
        montoFinalInput.addEventListener('input', function () {
            const montoInicial = Number(config.montoInicial || 0);
            const ventasDia = parseFloat(document.getElementById('ventas-dia').value.replace('$', '')) || 0;
            const montoFinal = parseFloat(this.value) || 0;
            const diferencia = montoFinal - (montoInicial + ventasDia);
            document.getElementById('diferencia-caja').value = diferencia.toFixed(2);
        });
    }
});
