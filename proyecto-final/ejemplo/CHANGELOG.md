# Changelog

Todas las modificaciones realizadas en el proyecto Tienda MVC.

## [1.0.0] - 2026-06-03

### Resumen general

Esta version introduce proteccion CSRF, bitacora de actividades, API REST, paginacion,
subida de imagenes, rutas amigables, validaciones de negocio, sticky footer,
y documentacion completa del codigo via DocBlocks.

---

### ARCHIVOS CREADOS

---

#### `helpers/Csrf.php` — Proteccion CSRF

Clase con metodos estaticos para generar y validar tokens CSRF.

- **`generar()`**: Inicia sesion si es necesario, genera un token de 64 caracteres
  hexadecimales via `random_bytes(32)` y lo almacena en `$_SESSION['csrf_token']`.
  Si ya existe un token, lo retorna sin regenerarlo (persistencia entre peticiones).

- **`campo()`**: Genera el HTML `<input type="hidden" name="csrf_token" value="...">`
  listo para incluir en formularios. Llama internamente a `generar()`.

- **`validar(?string $token)`**: Compara el token recibido contra el almacenado en
  sesion usando `hash_equals()` para mitigar ataques de timing. Retorna `false` si
  cualquiera de los dos esta vacio.

**Flujo tipico**: El formulario renderiza `<?= Csrf::campo() ?>`, el controlador
valida con `Csrf::validar($_POST['csrf_token'] ?? '')` antes de procesar los datos.

---

#### `models/LogModel.php` — Modelo de bitacora

Gestiona el registro y consulta de la tabla `bitacora`.

- **`__construct()`**: Establece conexion PDO via `Database::connect()`.

- **`registrar(int $adminId, string $adminUsername, string $accion, ?string $detalles)`**:
  Inserta un registro en la tabla `bitacora` con los datos del administrador,
  la accion realizada y detalles opcionales. Retorna `true/false`.

- **`obtenerTodos(int $page, int $perPage)`**: Devuelve registros paginados
  ordenados por fecha descendente. Calcula el offset como `(page - 1) * perPage`.

- **`contarTodos()`**: Cuenta el total de registros en la bitacora via `COUNT(*)`.

**Flujo tipico**: Un controlador instancia `LogModel`, llama a `registrar()` tras
cada accion del CRUD (crear, actualizar, eliminar, login, logout). La vista
`productos/bitacora.php` consume `obtenerTodos()` y `contarTodos()` para mostrar
el listado paginado.

---

#### `controllers/ApiController.php` — API REST de productos

Expone endpoints JSON para consultar productos.

- **`jsonResponse(mixed $data, int $status)`**: Metodo privado que establece
  `Content-Type: application/json`, codifica los datos con `json_encode()` usando
  `JSON_UNESCAPED_UNICODE`, asigna el codigo HTTP y termina la ejecucion.

- **`productos()`**: Obtiene todos los productos via `ProductoModel::obtenerTodos()`
  y los retorna como `{"success":true,"data":[...]}`.

- **`productoPorId(int $id)`**: Busca un producto por ID. Si no existe retorna
  `{"success":false,"error":"Producto no encontrado"}` con HTTP 404.

**Flujo tipico**: `GET /ejemplo/api/productos` → regex en `index.php` o case
`api/productos` → `ApiController::productos()` → JSON response.

---

#### `views/productos/bitacora.php` — Vista de bitacora

Tabla responsiva con columnas: ID, Administrador, Accion, Detalles, Fecha.

- Muestra paginacion Bootstrap si `$totalPages > 1`.
- Muestra "No hay registros" si `$logs` esta vacio.
- Enlace "Volver a productos" en la cabecera.
- Usa `BASE_URL` para todos los enlaces de paginacion.

---

#### `.htaccess` — Rutas amigables

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?route=$1 [QSA,L]
```

Redirige todas las peticiones que no sean archivos o directorios existentes hacia
`index.php` con el parametro `route`. Permite URLs como `/ejemplo/productos`,
`/ejemplo/productos/edit?id=5`, `/ejemplo/api/productos`, etc.

Requiere `mod_rewrite` habilitado y `AllowOverride All` en Apache.

---

#### `README.md` — Documentacion del proyecto

Incluye: requisitos, instalacion paso a paso, funcionalidades listadas,
estructura completa del proyecto y validaciones incluidas.

---

### ARCHIVOS MODIFICADOS

---

#### `database.sql` — Esquema de base de datos

**Agregado:**
- Columna `imagen varchar(255) DEFAULT NULL` en tabla `productos` — almacena el
  nombre del archivo de imagen subido.
- Columna `UNIQUE KEY sku (sku)` en `productos` — restriccion a nivel de BD para
  evitar SKUs duplicados.
- Tabla `bitacora` con columnas: `id` (PK autoincrement), `admin_id`, `admin_username`,
  `accion`, `detalles`, `created_at`.
- Primary keys explicitas (`AUTO_INCREMENT`) y `UNIQUE KEY username` en `usuarios`.

**Eliminado:** El `CREATE TABLE` original sin PKs ni UNIQUEs.

**Flujo**: Al importar `database.sql` se crean las 3 tablas con las restricciones
necesarias. La tabla `bitacora` no tiene FK para mantener simplicidad.

---

#### `config/Database.php` — Conexion PDO

**Corregido:** Bug en el DSN string.
- Antes: `"mysql:host=($this->host);dbname=($this->dbName);charset=($this->charset)"`
  — los parentesis literales causaban error de conexion.
- Despues: `"mysql:host={$this->host};dbname={$this->dbName};charset={$this->charset}"`
  — interpolacion correcta con llaves.

**Agregado:** DocBlocks a la clase (`@package Config`, `@author`, `@version`),
a cada propiedad (`@var string`) y al metodo `connect()` (`@return PDO`).

---

#### `config/Autoload.php` — Autoloader de clases

**Agregado:** DocBlock completo explicando que convierte el namespace en ruta de
archivo, con `@package Config`, `@author`, `@version`.

**Flujo**: Cuando se usa una clase como `Controllers\ProductoController`, el
autoloader transforma a `controllers/productoController.php` (primera parte en
minusculas) y la require.

---

#### `index.php` — Front Controller

**Agregado:**
- `BASE_URL` definida dinamicamente desde `$_SERVER['SCRIPT_NAME']` para que los
  enlaces funcionen independientemente del directorio de instalacion.
- Import de `Controllers\ApiController`.
- Instancia de `$apiController`.
- Regex `#^api/productos/(\d+)$#` para rutas como `api/productos/5` que matchean
  antes del `switch` y llaman a `productoPorId()`.
- Case `api/productos` en el switch para listado completo.

**Modificado:**
- DocBlock cabecera del archivo con `@package null`, `@author`, `@version`.

**Corregido:** Pagina en blanco tras fallo de autenticacion (y otras rutas POST).

- **Problema**: Las rutas POST como `auth/login`, `productos/store`,
  `productos/update` y `productos/delete` solo manejaban el metodo POST.
  Si el navegador llegaba a esas URLs via GET (por ejemplo, tras un redirect
  relativo desde `header('Location: login')` que el navegador resolvia como
  `/ejemplo/auth/login`), el switch no ejecutaba ninguna accion y mostraba
  una pagina en blanco. Esto ocurria porque los `header('Location: ...')` en
  los controladores usan rutas relativas, y al estar bajo un subdirectorio
  virtual (`auth/login`, `productos/store`, etc.), el navegador resuelve
  la ruta contra la URL actual, no contra la raiz del proyecto.

- **Solucion**: Se agrego un bloque `else` a los 4 casos del switch que
  solo esperaban POST. Ahora, si el metodo es GET:

  - `auth/login` → `showLogin()`: muestra el formulario de inicio de sesion,
    permitiendo al usuario reintentar sin necesidad de navegar manualmente.

  - `productos/store` → redirige a `BASE_URL . 'productos'`: redirige al
    listado de productos en vez de mostrar pagina vacia.

  - `productos/update` → redirige a `BASE_URL . 'productos'`: mismo
    comportamiento que store.

  - `productos/delete` → redirige a `BASE_URL . 'productos'`: mismo
    comportamiento que store.

  Las redirecciones GET usan `BASE_URL` (ruta absoluta) para evitar el
  mismo problema de resolucion relativa.

**Flujo de enrutamiento:**
1. `.htaccess` redirige `productos/edit?id=5` a `index.php?route=productos/edit&id=5`.
2. `index.php` obtiene `$_GET['route'] = 'productos/edit'`.
3. Regex de API se evalua primero (no matchea en este caso).
4. Switch llega a `case 'productos/edit'` → `$productoController->edit()`.
5. El controlador lee `$_GET['id']` para obtener el producto.

---

#### `controllers/ProductoController.php` — Controlador de productos

**Agregado metodos:**

- **`redirigirSiNoCsrf()`**: Valida el token CSRF y redirige a `productos` con
  mensaje de error si es invalido. Usado en `store()`, `update()`, `delete()`.

- **`registrarLog(string $accion, ?string $detalles)`**: Instancia `LogModel` y
  registra en bitacora con los datos del admin en sesion. Usado en `store()`,
  `update()`, `delete()`.

- **`procesarImagen(?array $file, ?string $imagenActual)`**: Maneja la subida de
  imagenes:
  1. Si no hay archivo o hay error, retorna la imagen actual (o vacio).
  2. Crea el directorio `views/img/productos/` si no existe.
  3. Valida extension: solo `jpg, jpeg, png, gif, webp`.
  4. Valida tamano maximo: 2MB.
  5. Si hay imagen anterior, la elimina del disco.
  6. Genera nombre unico via `uniqid('img_')` y mueve el archivo.
  7. Retorna el nombre del archivo o la imagen anterior si falla.

**Modificado metodos existentes:**

- **`store()`**: Se agregaron validaciones en este orden:
  1. CSRF via `redirigirSiNoCsrf()`.
  2. Campos obligatorios (seis campos).
  3. Tipos numericos en precios y existencia.
  4. `precioCompra >= 0` y `precioVenta >= 0`.
  5. `existencia >= 0` (validacion separada de "no negativos").
  6. `precioVenta >= precioCompra`.
  7. SKU unico via `ProductoModel::existeSku()`.
  8. Procesamiento de imagen via `procesarImagen()`.
  9. Registro en bitacora via `registrarLog()` si la creacion fue exitosa.

- **`update()`**: Mismas validaciones que `store()` mas:
  - Verifica ID valido (> 0).
  - Verifica que el producto exista antes de actualizar.
  - `existeSku()` recibe `$id` como `$excludeId` para ignorar el producto actual.
  - `procesarImagen()` recibe `$productoActual['imagen']` como imagen previa.
  - Elimina la imagen anterior del disco si se sube una nueva.

- **`delete()`**: Se agregaron:
  - CSRF via `redirigirSiNoCsrf()`.
  - Verificacion de ID valido.
  - Verificacion de existencia del producto.
  - Eliminacion fisica del archivo de imagen del disco antes de borrar el registro.
  - Registro en bitacora si la eliminacion fue exitosa.

- **`index()`**: Se agrego paginacion con `$page`, `$perPage = 10`, `$totalPages`.

- **`bitacora()`**: Nuevo metodo que muestra la bitacora paginada con
  `$perPage = 20`.

**Agregado:** DocBlocks a la clase y todos los metodos, incluyendo `@var` para
`$productoModel`, `@param`/`@return` donde aplica.

---

#### `controllers/AuthController.php` — Controlador de autenticacion

**Modificado:**

- **`login()`**: Se agregaron:
  - Validacion CSRF via `Csrf::validar()` al inicio del metodo.
  - Registro en bitacora via `LogModel::registrar()` tras login exitoso.

- **`logout()`**: Se agrego:
  - Registro en bitacora antes de destruir la sesion.
  - Verifica que `$_SESSION['admin']` exista para evitar errores.

**Flujo de login:**
1. Formulario envía POST a `auth/login` con `csrf_token`.
2. `AuthController::login()` valida CSRF → si falla, redirige con error.
3. Valida campos vacios.
4. Busca usuario por username.
5. Verifica password con `password_verify()`.
6. Si es correcto: guarda sesion, registra en bitacora, redirige a `productos`.
7. Si es incorrecto: redirige a `login` con mensaje de error.

---

#### `controllers/PublicController.php` — Controlador publico

**Modificado:**

- **`catalogo()`**: Se agrego paginacion:
  - `$page = max(1, (int)($_GET['page'] ?? 1))`.
  - `$perPage = 9` (3 columnas de tarjetas).
  - `$totalPages` calculado desde `contarBusqueda()`.
  - `buscarProducto()` ahora recibe parametros de paginacion.

---

#### `models/ProductoModel.php` — Modelo de productos

**Agregado metodos:**

- **`contarTodos()`**: Retorna el total de productos via `COUNT(*)`.

- **`contarBusqueda(string $termino)`**: Cuenta productos que coinciden con el
  termino de busqueda. Si el termino esta vacio, delega en `contarTodos()`.

- **`existeSku(string $sku, ?int $excludeId)`**: Verifica si un SKU ya existe en
  la BD. Acepta `$excludeId` opcional para excluir un producto especifico
  (usado en actualizacion para no detectar el propio SKU como duplicado).

**Modificado metodos existentes:**

- **`obtenerTodos()`**: Ahora acepta `$page` y `$perPage`. Calcula `$offset` y
  agrega `LIMIT :limit OFFSET :offset` a la consulta SQL.

- **`buscarProducto()`**: Ahora acepta `$page` y `$perPage`. Aplica el mismo
  LIMIT/OFFSET que `obtenerTodos()`.

- **`crear()`**: Se agrego la columna `imagen` al INSERT y su binding.

- **`actualizar()`**: Se agrego la columna `imagen` al SET y su binding.

**Agregado:** DocBlocks a la clase, `@var` para `$conexion`, y `@param`/`@return`
a todos los metodos.

---

#### `models/UsuarioModel.php` — Modelo de usuarios

**Agregado:** DocBlocks a la clase (`@package Models`, `@author`, `@version`),
`@var` para `$conexion`, `@param`/`@return` para `buscarPorUsername()`.

---

#### `views/layouts/header.php` — Cabecera del layout

**Modificado:**
- Navbar: enlaces ahora usan `BASE_URL`. Nav condicional: si hay sesion admin
  muestra botones "Admin" y "Cerrar sesion", si no muestra "Administrador".
- Body: `<body class="d-flex flex-column min-vh-100">` para sticky footer.
- Container: `<div class="container mt-4 flex-grow-1">` — `flex-grow-1` expande
  el contenido para empujar el footer al fondo.
- CSS agregado:
  - `.img-thumb-admin` — miniatura 60x60 centrada con `object-position: center`.
  - `.img-preview-edit` — preview 150x150 con `object-fit: contain` para ver
    la imagen completa sin recortes, fondo gris claro.
  - `.img-card-catalog` — imagen 100% de ancho, 200px de alto, cubre y centra.
  - `html,body{height:100%}` — refuerzo para el sticky footer.
- Error: corregido `alert-success` → `alert-danger` en el mensaje de error.

---

#### `views/layouts/footer.php` — Pie del layout

**Reemplazado completamente:**
- Antes: solo `</div></body></html>`.
- Despues: cierra el container, agrega footer Bootstrap responsivo:
  - `bg-dark text-light` para coherencia con el navbar.
  - 3 columnas en `md+`: info del sistema, enlaces, copyright.
  - Una columna en movil (apilado vertical).
  - Enlaces a Catalogo y Admin con `text-decoration-none`.
  - Copyright dinamico con `date('Y')`.

---

#### `views/auth/login.php` — Formulario de login

**Modificado:**
- Form action: `index.php?route=auth/login` → `<?= BASE_URL ?>auth/login`.
- Agregado: `<?= Csrf::campo(); ?>` como primer campo del formulario.

---

#### `views/productos/index.php` — Listado de productos

**Modificado:**
- Enlaces: todos usan `BASE_URL`.
- Agregado boton "Bitacora" en la cabecera.
- Agregada columna "Imagen" en la tabla con miniatura `img-thumb-admin`.
- Formulario de eliminar: incluye `<?= Csrf::campo(); ?>`.
- Agregada paginacion Bootstrap al final (solo si `$totalPages > 1`).
- Corregido bug: `$producto('id')` → `$producto['id']`.

---

#### `views/productos/create.php` — Formulario de creacion

**Modificado:**
- Form action: usa `BASE_URL`.
- `enctype="multipart/form-data"` para subida de archivos.
- Agregado `<?= Csrf::campo(); ?>`.
- Agregado campo `input type="file" name="imagen"` con filtro `accept`.

---

#### `views/productos/edit.php` — Formulario de edicion

**Corregido:**
- Form action: `index.php?route=productos/store` → `<?= BASE_URL ?>productos/update`.
- Variable: `$productos['sku']` → `$producto['sku']` (y todos los campos).
- Agregado hidden `name="id"` con el ID del producto.

**Modificado:**
- `enctype="multipart/form-data"`.
- `<?= Csrf::campo(); ?>`.
- Preview de imagen actual con clase `img-preview-edit` y mensaje explicativo.
- Campo file para reemplazar imagen.
- Enlaces con `BASE_URL`.

---

#### `views/public/catalogo.php` — Catalogo publico

**Modificado:**
- Form action: usa `BASE_URL`.
- Imagen del producto: clase `img-card-catalog` en vez de estilos inline.
- Agregada paginacion Bootstrap (mantiene el termino de busqueda en los enlaces).
- Ruta de imagen: `views/img/productos/` en vez de `uploads/productos/`.

---

### ARCHIVOS ELIMINADOS

---

#### `uploads/` (directorio completo)

Movido a `views/img/productos/` para mantener todas las imagenes dentro de la
estructura de vistas del MVC. Contenido migrado: no habia archivos reales,
solo el `.gitkeep`.

#### `README.txt`

Reemplazado por `README.md` con formato Markdown, estructura mejorada y
contenido actualizado.

---

### BUGS CORREGIDOS

---

1. **`config/Database.php:17`** — DSN mal formado
   - Sintaxis: `host=($this->host)` usaba parentesis en vez de llaves para
     interpolacion. Causaba error de conexion PDO.
   - Correccion: `host={$this->host}`.

2. **`index.php:60`** — Sintaxis incorrecta en switch
   - `default;` (punto y coma) en vez de `default:` (dos puntos).
   - Correccion: `default:`.

3. **`views/productos/index.php:34`** — Llamada a funcion en vez de array
   - `$producto('id')` intentaba invocar `$producto` como funcion.
   - Correccion: `$producto['id']`.

4. **`views/productos/edit.php:5`** — Action apuntaba a `store`
   - El formulario de edicion enviaba a `productos/store` en vez de `productos/update`.
   - Correccion: `action="<?= BASE_URL ?>productos/update"`.

5. **`views/productos/edit.php`** — Nombre de variable incorrecto
   - Usaba `$productos` (plural) para acceder a los datos, pero el controlador
     asignaba `$producto` (singular).
   - Correccion: `$producto['campo']`.

6. **`views/layouts/header.php:29-31`** — Clase de alerta incorrecta
   - El mensaje de error usaba `alert-success` (verde) en vez de `alert-danger` (rojo).
   - Correccion: `alert-danger`.

7. **`views/layouts/header.php` / `footer.php`** — Estructura HTML anidada
   - El header abria un `<div class="container mt-4">`, mostraba las alerts
     y lo cerraba. El footer tenia un `</div>` extra sin apertura.
   - Correccion: el container ahora envuelve todo el contenido de la pagina
     (no solo las alerts), y el footer cierra ese unico container.

8. **`index.php:44-93`** — Pagina en blanco en rutas POST con metodo GET
   - **Sintomas**: Al ingresar credenciales incorrectas, la pagina redirigia
     a una pantalla en blanco en vez de mostrar el formulario con un mensaje
     de error.
   - **Diagnostico**: `AuthController::login()` ejecuta `header('Location: login')`
     tras un fallo. `'login'` es una URL relativa que el navegador resuelve
     contra la URL actual `/ejemplo/auth/login`, resultando en la misma ruta
     `auth/login` pero con metodo GET. En `index.php`, `case 'auth/login'`
     solo ejecutaba codigo si el metodo era POST. Al ser GET, no entraba al
     `if` y no habia `else`, por lo que el switch pasaba sin ejecutar nada
     y la salida era un documento HTML vacio (pagina en blanco).
   - **Causa raiz**: Los `header('Location: ...')` en los controladores usan
     rutas relativas. Cuando el navegador esta en una URL con subdirectorio
     virtual (ej. `/ejemplo/auth/login`), la ruta relativa `login` se resuelve
     contra ese subdirectorio, dando la misma URL con GET en vez de POST.
   - **Impacto**: Afectaba a 4 rutas: `auth/login`, `productos/store`,
     `productos/update` y `productos/delete`. Cualquier GET a estas rutas
     producia pagina en blanco.
   - **Correccion**: Se agregaron bloques `else` a los 4 casos del switch
     en `index.php`:
     - `auth/login` GET → muestra el formulario de login (`showLogin()`)
     - `productos/store/update/delete` GET → redirige a `productos` usando
       `BASE_URL` (ruta absoluta)
   - **Alternativa descartada**: Modificar todas las redirecciones en los
     controladores para usar `BASE_URL` fue descartada por ser un cambio
     masivo e innecesario cuando el problema se resuelve en una sola linea
     por ruta en el Front Controller.

---

### DOCUMENTACION AGREGADA (DocBlocks)

Los siguientes 11 archivos recibieron DocBlocks completos siguiendo el estandar
PHPDoc con estilo Mintlify (descripciones concisas en español):

| Archivo | Paquete | Clases | Propiedades `@var` | Metodos documentados |
|---|---|---|---|---|
| `config/Database.php` | `Config` | `Database` | 5 (`$host`, `$dbName`, `$username`, `$password`, `$charset`) | `connect()` |
| `config/Autoload.php` | `Config` | _(closure)_ | — | — |
| `controllers/AuthController.php` | `Controllers` | `AuthController` | — | `showLogin()`, `login()`, `logout()` |
| `controllers/ProductoController.php` | `Controllers` | `ProductoController` | 1 (`$productoModel`) | 11 metodos |
| `controllers/PublicController.php` | `Controllers` | `PublicController` | — | `catalogo()` |
| `controllers/ApiController.php` | `Controllers` | `ApiController` | 1 (`$productoModel`) | `jsonResponse()`, `productos()`, `productoPorId()` |
| `helpers/Csrf.php` | `Helpers` | `Csrf` | — | `generar()`, `campo()`, `validar()` |
| `models/ProductoModel.php` | `Models` | `ProductoModel` | 1 (`$conexion`) | 9 metodos |
| `models/UsuarioModel.php` | `Models` | `UsuarioModel` | 1 (`$conexion`) | `buscarPorUsername()` |
| `models/LogModel.php` | `Models` | `LogModel` | 1 (`$conexion`) | `registrar()`, `obtenerTodos()`, `contarTodos()` |
| `index.php` | `null` | _(Front Controller)_ | — | — |

Cada DocBlock de clase incluye: descripcion comenzando con "Clase que...",
`@package`, `@author Tienda MVC`, `@version 1.0.0`.

Cada DocBlock de propiedad incluye: descripcion breve en linea propia,
`@var tipo`.

Cada DocBlock de metodo incluye: descripcion breve del proposito,
`@param tipo $nombre Descripcion` (cuando aplica),
`@return tipo Descripcion` (cuando no es `void`).

---

### ESTRUCTURA FINAL DEL PROYECTO

```
ejemplo/
├── config/
│   ├── Autoload.php
│   └── Database.php
├── controllers/
│   ├── ApiController.php
│   ├── AuthController.php
│   ├── ProductoController.php
│   └── PublicController.php
├── helpers/
│   └── Csrf.php
├── models/
│   ├── LogModel.php
│   ├── ProductoModel.php
│   └── UsuarioModel.php
├── views/
│   ├── img/
│   │   └── productos/
│   ├── auth/
│   │   └── login.php
│   ├── layouts/
│   │   ├── footer.php
│   │   └── header.php
│   ├── productos/
│   │   ├── bitacora.php
│   │   ├── create.php
│   │   ├── edit.php
│   │   └── index.php
│   └── public/
│       └── catalogo.php
├── .htaccess
├── CHANGELOG.md
├── database.sql
├── example.htaccess
├── index.php
└── README.md
```
