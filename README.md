# Sistema de Gestión de Tienda (POS)

Manual técnico básico del proyecto. Describe ambientes, dependencias por capa, archivos ejecutables y referencias de despliegue.



**Repositorio:** [https://github.com/felipearauen/creativ](https://github.com/felipearauen/creativ)

---

## 1. Descripción general

Aplicación web para gestión de inventario, punto de venta (POS), control de caja y reportes de ventas.

| Aspecto | Detalle |
|---------|---------|
| Tipo | Monolito PHP con vistas separadas y API JSON interna |
| Alcance | Uso local (XAMPP) y despliegue en hosting PHP/MySQL |
| Idioma UI | Español |
| Autenticación | Sesión PHP (`PHPSESSID`) + roles |

**Módulos principales**

- Autenticación y registro de usuarios
- Gestión de productos (CRUD, admin / inventario)
- Punto de venta con apertura/cierre de caja
- Reportes con gráficos y exportación Excel/PDF
- Endpoints JSON para operaciones AJAX del POS

---

## 2. Ambientes

### 2.1 Desarrollo (local)

| Ítem | Valor |
|------|--------|
| Servidor | Apache (XAMPP) |
| PHP | 8.x recomendado (compatible con 7.4+) |
| Base de datos | MySQL / MariaDB |
| Ruta típica | `C:\xampp123\htdocs\Tienda` |
| URL local | `http://localhost/Tienda/` |
| Login | `http://localhost/Tienda/login.php` |
| Config BD | `config/database.php` |

Credenciales por defecto en desarrollo:

```
DB_HOST = localhost
DB_NAME = tienda_db
DB_USER = root
DB_PASS = (vacío en XAMPP)
```

### 2.2 Pruebas

| Ítem | Valor |
|------|--------|
| Objetivo | Validar login, CRUD productos, POS, reportes y APIs |
| Datos | `sql/datos_prueba.sql` (después de `sql/database.sql`) |
| Herramientas | Navegador + Postman (APIs con cookie de sesión) |
| Usuarios de prueba | Ver sección 8 |

Flujo sugerido de prueba:

1. Importar esquema y datos de prueba.
2. Login con `admin` → productos y reportes.
3. Login con `cajero1` → POS (caja abierta del día en datos seed).
4. Verificar APIs desde Postman tras autenticarse.

### 2.3 Producción / despliegue en línea

Ajustar en el hosting:

1. Subir el código al document root (o subcarpeta).
2. Crear la base `tienda_db` e importar `sql/database.sql`.
3. Actualizar `config/database.php` con host, usuario y contraseña del hosting.
4. (Opcional) Colocar FPDF en `vendor/fpdf/` o `fpdf186/` para exportar PDF.
5. Verificar que Apache tenga `mod_rewrite` si se usan reglas propias (no obligatorio en la versión actual).

| Ambiente | URL base | Notas |
|----------|----------|--------|
| Desarrollo | `http://localhost/Tienda/` | XAMPP local |
| Pruebas | _Completar por el equipo_ | Staging / subdominio de prueba |
| Producción | _Completar por el equipo_ | Hosting definitivo |

> Sustituir las filas de pruebas/producción con los enlaces reales del despliegue cuando estén disponibles.

---

## 3. Stack y dependencias por capa

### 3.1 Capa de presentación (Frontend)

| Tecnología | Versión / origen | Uso |
|------------|------------------|-----|
| HTML5 + PHP views | — | Markup en `views/` |
| CSS propio | `assets/css/` | Estilos por módulo |
| JavaScript propio | `assets/js/` | POS, productos, reportes |
| Bootstrap | 5.3.0 (CDN) | Layout, formularios, modales |
| Font Awesome | 6.0.0 (CDN) | Iconografía |
| Chart.js | CDN | Gráficos en reportes |

CDN utilizados:

- `https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/...`
- `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/...`
- `https://cdn.jsdelivr.net/npm/chart.js`

No requiere npm ni bundler: los assets locales se sirven estáticos y las librerías UI van por CDN.

### 3.2 Capa de aplicación (Backend PHP)

| Tecnología | Uso |
|------------|-----|
| PHP (procedural + includes) | Controladores en la raíz y `api/` |
| Sesiones PHP | Login y control de roles |
| PDO | Acceso a MySQL |
| JSON | Respuestas de la API interna |

Archivos transversales:

| Archivo | Rol |
|---------|-----|
| `includes/bootstrap.php` | Carga config, auth y helpers |
| `includes/auth.php` | `checkLogin()`, `checkRole()`, helpers de rol |
| `includes/helpers.php` | Escape HTML, redirect, formato de moneda |
| `config/database.php` | Conexión PDO |

### 3.3 Capa de datos

| Tecnología | Detalle |
|------------|---------|
| MySQL / MariaDB | Motor relacional |
| Esquema | `sql/database.sql` |
| Seed de pruebas | `sql/datos_prueba.sql` |

Tablas principales: `usuarios`, `productos`, `cajas`, `ventas`, `detalle_ventas`, `turnos`.

### 3.4 Librerías opcionales

| Librería | Ubicación esperada | Uso |
|----------|--------------------|-----|
| FPDF | `vendor/fpdf/fpdf.php` o `fpdf186/fpdf.php` | Exportación PDF de reportes |

Sin FPDF, la exportación a Excel sigue disponible; el PDF responde con mensaje de librería faltante.

---

## 4. Estructura del proyecto

```
Tienda/
├── api/                    # Endpoints JSON / exportación
│   ├── pos_actions.php
│   ├── get_products.php
│   ├── get_product_by_barcode.php
│   ├── guardar_producto.php
│   ├── process_sale.php
│   └── export_report.php
├── assets/
│   ├── css/                # app, auth, productos, pos, reportes
│   └── js/                 # productos, pos, reportes
├── config/
│   └── database.php
├── includes/
│   ├── bootstrap.php
│   ├── auth.php
│   └── helpers.php
├── sql/
│   ├── database.sql
│   └── datos_prueba.sql
├── views/
│   ├── auth/
│   ├── layouts/
│   ├── productos/
│   ├── pos/
│   └── reportes/
├── index.php               # Productos
├── login.php
├── logout.php
├── registro.php
├── pos.php
├── reportes.php
├── unauthorized.php
└── README.md
```

---

## 5. Archivos ejecutables / puntos de entrada

Páginas web (navegador):

| Archivo | Descripción | Roles típicos |
|---------|-------------|---------------|
| `login.php` | Inicio de sesión | Público |
| `registro.php` | Alta de usuario (`rol = usuario`) | Público |
| `logout.php` | Cierre de sesión | Autenticado |
| `index.php` | CRUD de productos | admin, inventario |
| `pos.php` | Punto de venta / caja | cajero, admin, usuario |
| `reportes.php` | Dashboard de ventas | admin |
| `unauthorized.php` | Acceso denegado | Autenticado |

API interna (`api/`):

| Endpoint | Método | Descripción |
|----------|--------|-------------|
| `api/pos_actions.php` | POST | Buscar producto / procesar venta |
| `api/get_products.php` | GET | Búsqueda de productos con stock |
| `api/get_product_by_barcode.php` | GET | Lookup por código de barras |
| `api/guardar_producto.php` | POST | Alta rápida de producto |
| `api/process_sale.php` | POST (JSON) | Venta alternativa |
| `api/export_report.php` | GET | Export Excel (`tipo=excel`) o PDF (`tipo=pdf`) |

Scripts SQL:

| Archivo | Acción |
|---------|--------|
| `sql/database.sql` | Crea BD, tablas e índices; inserta admin y turnos |
| `sql/datos_prueba.sql` | Limpia datos transaccionales e inserta catálogo, usuarios, cajas y ventas de prueba |

---

## 6. Instalación en desarrollo

1. Clonar o copiar el proyecto en `htdocs` (ej. `C:\xampp123\htdocs\Tienda`).
2. Iniciar **Apache** y **MySQL** desde el panel de XAMPP.
3. En phpMyAdmin o CLI:
   - Ejecutar `sql/database.sql`
   - Ejecutar `sql/datos_prueba.sql`
4. Abrir `http://localhost/Tienda/login.php`
5. Ingresar con un usuario de la sección 8.

Si la conexión falla, revisar `config/database.php` y que exista la base `tienda_db`.

---

## 7. Configuración de dependencias

Este proyecto **no usa Composer ni `package.json`** para el núcleo.

| Dependencia | Cómo se obtiene |
|-------------|-----------------|
| PHP + MySQL + Apache | XAMPP / hosting |
| Bootstrap, Font Awesome, Chart.js | CDN (requiere Internet en runtime) |
| FPDF (PDF) | Manual: descargar y ubicar en `vendor/fpdf/` o `fpdf186/` |

Para un entorno sin Internet, descargar los CSS/JS de Bootstrap, Font Awesome y Chart.js y referenciarlos desde `assets/`.

---

## 8. Usuarios de prueba

Tras cargar `datos_prueba.sql`, la contraseña de todos es: **`password`**

| Usuario | Rol | Acceso sugerido |
|---------|-----|-----------------|
| `admin` | admin | Productos, POS, reportes |
| `cajero1` | cajero | POS (incluye caja abierta del día en el seed) |
| `cajero2` | cajero | POS |
| `invent1` | inventario | Productos |
| `vendedor1` | usuario | POS |

Admin por defecto del esquema (`database.sql`): `admin` / `password`.

---

## 9. Verificación de APIs (Postman)

Las APIs dependen de **cookie de sesión**. No usan Bearer token.

1. **POST** `http://localhost/Tienda/login.php`  
   Body `x-www-form-urlencoded`: `usuario`, `password`, `login=1`
2. Confirmar que Postman guardó `PHPSESSID` para `localhost`.
3. Llamar endpoints, por ejemplo:
   - **GET** `api/get_products.php?search=arroz`
   - **POST** `api/pos_actions.php` con `action=buscar_producto` y `codigo=AB001`

Sin sesión válida, la respuesta suele ser redirección HTML a `login.php` (no JSON).

---

## 10. Enlaces de despliegue

Documentar aquí las URLs oficiales del equipo:

| Ambiente | URL | Responsable | Fecha |
|----------|-----|-------------|-------|
| Desarrollo | `http://localhost/Tienda/` | Equipo local | — |
| Pruebas | `https://...` | _Por definir_ | — |
| Producción | `https://...` | _Por definir_ | — |
| Repositorio | [github.com/felipearauen/creativ](https://github.com/felipearauen/creativ) | Equipo | — |
| Documentación / wiki | `https://...` | _Por definir_ | — |

Checklist post-despliegue:

- [ ] `config/database.php` apunta al servidor de producción
- [ ] Esquema importado y backups configurados
- [ ] Login funcional con usuario administrador
- [ ] POS abre/cierra caja y registra ventas
- [ ] Reportes y exportación Excel verificados
- [ ] PDF verificado (si FPDF está instalado)
- [ ] HTTPS habilitado en producción

---

## 11. Roles y permisos (resumen)

| Recurso | admin | cajero | inventario | usuario |
|---------|:-----:|:------:|:----------:|:-------:|
| Productos (`index.php`) | Sí | — | Sí | — |
| POS (`pos.php`) | Sí | Sí | — | Sí |
| Reportes | Sí | — | — | — |
| `api/pos_actions.php` | Sí | Sí | — | Sí |
| `api/guardar_producto.php` | Sí | — | Sí | — |
| `api/process_sale.php` | Sí | Sí | — | — |
| `api/export_report.php` | Sí | — | — | — |

---

## 12. Convenciones de código

- **PHP:** camelCase en funciones y variables de aplicación; archivos en `snake_case.php`.
- **JS:** camelCase en funciones; un archivo por módulo en `assets/js/`.
- **CSS:** kebab-case en clases; un archivo por módulo en `assets/css/`.
- **Vistas:** HTML/PHP en `views/`; la lógica de negocio permanece en los entry points y en `api/`.
- **Comentarios:** breves, orientados a mantenimiento.

---

## 13. Solución de problemas frecuentes

| Síntoma | Posible causa |
|---------|----------------|
| Error de conexión PDO | MySQL apagado o BD inexistente |
| Import `TRUNCATE` / FK | Usar `datos_prueba.sql` actual (usa `DELETE`, no `TRUNCATE`) |
| API devuelve HTML de login | Falta cookie de sesión en Postman |
| `No hay caja abierta` | Abrir caja en POS o usar `cajero1` tras el seed |
| PDF no genera | Instalar FPDF en la ruta documentada |
| Estilos rotos | Sin acceso a CDN; verificar red o hostear assets localmente |

---


*Documento técnico del proyecto Tienda — ambiente local XAMPP y despliegue PHP/MySQL.*
