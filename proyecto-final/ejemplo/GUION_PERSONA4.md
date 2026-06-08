# Guion de presentacion — Persona 4

## Autenticacion, API REST y Front Controller

---

## Tabla de contenidos

1. [controllers/AuthController.php](#1-controllersauthcontrollerphp)
2. [controllers/ApiController.php](#2-controllersapicontrollerphp)
3. [controllers/PublicController.php](#3-controllerspubliccontrollerphp)
4. [index.php](#4-indexphp)

---

## 1. controllers/AuthController.php

### Antes (codigo original)

La clase solo tenia 3 metodos sin seguridad ni trazabilidad:

```php
class AuthController
{
    public function showLogin(): void
    {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void
    {
        // validaba campos vacios
        // buscaba usuario en BD
        // password_verify
        // guardaba en $_SESSION['admin']
        // redirigia a index.php?route=productos
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: index.php?route=login');
    }
}
```

**Problemas:**
- No habia token CSRF — cualquiera podia enviar un formulario falso
- No se registraba en bitacora — no habia auditoria de quienes ingresaban
- Usaba `index.php?route=` en vez de rutas amigables

### Despues (mejoras aplicadas)

#### showLogin() — Sin cambios estructurales

Sigue cargando la misma vista, pero ahora la vista incluye `<?= Csrf::campo() ?>`.

---

#### login() — Se agregaron 2 capas de seguridad

**Paso 1: Validacion CSRF**
```php
if (!Csrf::validar($_POST['csrf_token'] ?? '')) {
    $_SESSION['error'] = 'Token de seguridad invalido.';
    header('Location: login');
    exit;
}
```
`Csrf::validar()` compara el token enviado desde el formulario contra el almacenado en `$_SESSION['csrf_token']`. Usa `hash_equals()` para evitar ataques de timing. Si no coincide → rechaza la peticion.

**Paso 2: Registro en bitacora tras login exitoso**
```php
$log = new LogModel();
$log->registrar(
    $usuario['id'],
    $usuario['username'],
    'Inicio de sesion',
    'El administrador inició sesion en el sistema'
);
```

**Flujo completo del login:**
```
Usuario llena formulario → POST a /auth/login
  → index.php case 'auth/login'
    → AuthController::login()
      1. ¿Token CSRF valido?  NO → redirige a login con error
      2. ¿Campos vacios?       SI → redirige a login con error
      3. Buscar usuario en BD  NO → redirige a login con error
      4. password_verify()     NO → redirige a login con error
      5. Guardar sesion
      6. Registrar en bitacora
      7. Redirigir a productos
```

#### logout() — Se agrego bitacora

Antes solo destruia la sesion. Ahora registra el cierre:
```php
if (isset($_SESSION['admin'])) {
    $log = new LogModel();
    $log->registrar(
        $_SESSION['admin']['id'],
        $_SESSION['admin']['username'],
        'Cierre de sesion',
        'El administrador cerró sesion en el sistema'
    );
}
session_destroy();
```
La verificacion `isset($_SESSION['admin'])` evita errores si se llama a logout sin estar autenticado.

---

## 2. controllers/ApiController.php

### Antes

**No existia.** El sistema no tenia API REST.

### Despues (creado desde cero)

Se creo para exponer los productos en formato JSON, permitiendo que aplicaciones externas (React, Vue, app movil, etc.) consuman los datos.

#### jsonResponse() — Metodo auxiliar privado

```php
private function jsonResponse(mixed $data, int $status = 200): void
{
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
```

**¿Que hace cada linea?**
1. `Content-Type: application/json` — le dice al navegador que es JSON
2. `charset=utf-8` — soporta acentos y eñes
3. `http_response_code()` — asigna el codigo HTTP (200 OK, 404 Not Found, etc.)
4. `json_encode()` con `JSON_UNESCAPED_UNICODE` — convierte el array a JSON sin escaparse los caracteres Unicode
5. `exit` — detiene la ejecucion para que no se renderice HTML

#### productos() — Listado completo

```php
public function productos(): void
{
    $productos = $this->productoModel->obtenerTodos();
    $this->jsonResponse(['success' => true, 'data' => $productos]);
}
```

**Respuesta ejemplo:**
```json
GET /api/productos

{
  "success": true,
  "data": [
    {"id": 1, "sku": "ABC123", "nombre": "Producto 1", ...},
    {"id": 2, "sku": "DEF456", "nombre": "Producto 2", ...}
  ]
}
```

#### productoPorId() — Producto individual

```php
public function productoPorId(int $id): void
{
    $producto = $this->productoModel->obtenerPorId($id);
    if (!$producto) {
        $this->jsonResponse([
            'success' => false,
            'error' => 'Producto no encontrado'
        ], 404);
    }
    $this->jsonResponse(['success' => true, 'data' => $producto]);
}
```

**Casos de uso:**
```
GET /api/productos/5  → 200 {"success":true, "data":{...}}
GET /api/productos/99 → 404 {"success":false, "error":"Producto no encontrado"}
```

---

## 3. controllers/PublicController.php

### Antes

El catalogo cargaba **todos** los productos sin paginacion:

```php
public function catalogo(): void
{
    $termino = trim($_GET['buscar'] ?? '');
    $productoModel = new ProductoModel();
    $productos = $productoModel->buscarProducto($termino);
    require_once __DIR__ . '/../views/public/catalogo.php';
}
```

Si habia 500 productos, se cargaban 500 en una sola pagina.

### Despues

Se agrego paginacion. Ahora se muestran **9 productos por pagina**:

```php
public function catalogo(): void
{
    $termino = trim($_GET['buscar'] ?? '');
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 9;

    $productoModel = new ProductoModel();
    $productos = $productoModel->buscarProducto($termino, $page, $perPage);
    $total = $productoModel->contarBusqueda($termino);
    $totalPages = max(1, (int)ceil($total / $perPage));

    require_once __DIR__ . '/../views/public/catalogo.php';
}
```

**Variables que se pasan a la vista:**
- `$productos` — solo los 9 de la pagina actual
- `$page` — pagina actual (ej. 1, 2, 3...)
- `$totalPages` — total de paginas calculado con ceil(total / perPage)
- `$termino` — se mantiene en los enlaces de paginacion

El modelo ahora tiene `LIMIT :limit OFFSET :offset` en las consultas SQL, asi que la BD solo devuelve los registros necesarios.

---

## 4. index.php

### Antes (codigo original)

```php
$route = $_GET['route'] ?? 'catalogo';

$authController = new AuthController();
$productoController = new ProductoController();
$publicController = new PublicController();

switch ($route) {
    case 'login':        $authController->showLogin();        break;
    case 'auth/login':   if(POST) $authController->login();    break;
    case 'logout':       $authController->logout();            break;
    case 'productos':    $productoController->index();         break;
    // ...mas rutas...
    case 'catalogo':
    default:             $publicController->catalogo();        break;
}
```

### Despues — Mejora 1: BASE_URL

```php
$baseDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
define('BASE_URL', $baseDir . '/');
```

**¿Que hace?**
- `$_SERVER['SCRIPT_NAME']` → `/ejemplo/index.php`
- `dirname()` → `/ejemplo`
- `BASE_URL` → `/ejemplo/`

Todas las vistas usan `<?= BASE_URL ?>productos` en vez de rutas relativas. Si el proyecto se copia a `localhost/tienda/`, BASE_URL sera `/tienda/` y todos los enlaces siguen funcionando.

---

### Despues — Mejora 2: API REST

Se agregaron:
```php
use Controllers\ApiController;
$apiController = new ApiController();

// Regex antes del switch para api/productos/{id}
if (preg_match('#^api/productos/(\d+)$#', $route, $matches)) {
    $apiController->productoPorId((int)$matches[1]);
}

// Dentro del switch:
case 'api/productos':
    $apiController->productos();
    break;
```

La regex permite que `api/productos/5` se capte antes que el switch, porque no hay un `case 'api/productos/5'` fijo. Se usa `preg_match()` con el patron `\d+` para extraer el ID numerico.

---

### Despues — Mejora 3: Ruta bitacora

```php
case 'productos/bitacora':
    $productoController->bitacora();
    break;
```

Simple, redirige al metodo `bitacora()` de `ProductoController` que muestra el historial de actividades.

---

### Despues — Mejora 4: Bug corregido (pagina en blanco)

**Este es el punto mas importante de tu presentacion.**

**El problema:**

1. Usuario ingresa credenciales incorrectas en `/ejemplo/auth/login`
2. `AuthController::login()` ejecuta `header('Location: login')`
3. El navegador recibe esa cabecera. `'login'` es una **ruta relativa**
4. El navegador la resuelve contra la URL actual: `/ejemplo/auth/login`
5. La peticion GET llega a `index.php?route=auth/login`
6. En el switch: `case 'auth/login'` solo tiene `if (POST) { ... }`
7. Al ser GET, no entra al `if`... y **no hay codigo que se ejecute**
8. Resultado: **pagina en blanco**

**La solucion (4 lineas agregadas):**

```php
case 'auth/login':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $authController->login();
    } else {
        $authController->showLogin();    // ← NUEVO: muestra login
    }
    break;

case 'productos/store':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $productoController->store();
    } else {
        header('Location: ' . BASE_URL . 'productos');  // ← NUEVO
        exit;
    }
    break;

case 'productos/update':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $productoController->update();
    } else {
        header('Location: ' . BASE_URL . 'productos');  // ← NUEVO
        exit;
    }
    break;

case 'productos/delete':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $productoController->delete();
    } else {
        header('Location: ' . BASE_URL . 'productos');  // ← NUEVO
        exit;
    }
    break;
```

**¿Por que no se cambiaron las redirecciones en los controladores?**
- Modificar `header('Location: login')` a `header('Location: ' . BASE_URL . 'login')` en todos los controladores implicaba tocar decenas de lineas en varios archivos
- Era mas limpio y centralizado resolverlo en el **unico punto de entrada**: `index.php`
- La regla es: **toda ruta POST debe tener un comportamiento definido para GET**

---

## Mapa mental para la presentacion

```
┌─────────────────────────────────────────────────────┐
│                                                     │
│   index.php (Front Controller)                      │
│   ├── Recibe la ruta                                │
│   ├── Define BASE_URL                               │
│   ├── Regex para API                                │
│   ├── Switch:                                       │
│   │   ├── login      → AuthController::showLogin()  │
│   │   ├── auth/login → POST→login() / GET→showLogin │
│   │   ├── logout     → AuthController::logout()     │
│   │   ├── catalogo   → PublicController::catalogo() │
│   │   └── api/*      → ApiController               │
│   └── Bug fix: else en rutas POST                   │
│                                                     │
├─────────────────────────────────────────────────────┤
│                                                     │
│   AuthController                                    │
│   ├── showLogin()   → renderiza formulario          │
│   ├── login()       → CSRF + validar + bitacora     │
│   └── logout()      → bitacora + session_destroy    │
│                                                     │
├─────────────────────────────────────────────────────┤
│                                                     │
│   PublicController                                  │
│   └── catalogo()    → busqueda + PAGINACION (nuevo) │
│                                                     │
├─────────────────────────────────────────────────────┤
│                                                     │
│   ApiController (NUEVO)                             │
│   ├── jsonResponse() → respuesta JSON estandarizada │
│   ├── productos()    → GET /api/productos           │
│   └── productoPorId()→ GET /api/productos/{id}      │
│                                                     │
└─────────────────────────────────────────────────────┘
```

---

## Tips para la presentacion

1. **Empieza con index.php** — es el punto de entrada, explica el flujo general
2. **Enfatiza el bug de pagina en blanco** — es el cambio mas visible y muestra pensamiento critico
3. **Muestra el antes/despues en paralelo** — mas claro que solo leer el codigo nuevo
4. **ApiController es el mas facil de explicar** — 3 metodos cortos, endpoint claro
5. **Duración estimada**: 6-8 minutos
