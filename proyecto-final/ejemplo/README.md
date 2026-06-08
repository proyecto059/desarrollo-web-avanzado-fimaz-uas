# Tienda MVC

Sistema web de tienda desarrollado con PHP siguiendo la arquitectura MVC, utilizando POO, PDO, Namespaces y Autoload.

## Requisitos

- PHP 8.0 o superior
- MySQL 5.7 o superior
- Apache con mod_rewrite habilitado
- XAMPP / WAMP / LAMP

## Instalacion

1. Clonar el repositorio en `C:\xampp\htdocs\ejemplo` (o la carpeta de tu servidor web)

2. Importar la base de datos:
   - Abrir phpMyAdmin
   - Crear una base de datos llamada `tienda_mvc`
   - Importar el archivo `database.sql`

3. Configurar la conexion en `config/Database.php` (usuario, contraseña, host)

4. Habilitar mod_rewrite en Apache:
   - En XAMPP: editar `apache/conf/httpd.conf` y descomentar `LoadModule rewrite_module modules/mod_rewrite.so`
   - En `httpd.conf` asegurar que AllowOverride este en `All` para el directorio del proyecto

5. Crear un usuario administrador en la base de datos:
   ```sql
   INSERT INTO usuarios (username, password, nombre_completo)
   VALUES ('admin', '$2y$10$...hash...', 'Administrador');
   ```
   (Generar el hash con `password_hash('tu_contraseña', PASSWORD_BCRYPT)`)

6. Acceder via navegador a `http://localhost/ejemplo/`

## Funcionalidades

- **Catalogo publico**: Visualizacion de productos con busqueda y paginacion
- **Administracion de productos**: CRUD completo con validaciones
- **Autenticacion**: Login/Logout de administradores
- **Proteccion CSRF**: Tokens de seguridad en todos los formularios
- **Paginacion**: Navegacion paginada en listados
- **Subida de imagenes**: Carga de imagenes para productos
- **Bitacora**: Registro de actividades del administrador
- **Rutas amigables**: URLs limpias mediante .htaccess

## Estructura del proyecto

```
ejemplo/
├── config/
│   ├── Autoload.php      # Carga automatica de clases
│   └── Database.php      # Conexion a la base de datos
├── controllers/
│   ├── AuthController.php      # Controlador de autenticacion
│   ├── ProductoController.php  # Controlador de productos
│   └── PublicController.php    # Controlador publico
├── helpers/
│   └── Csrf.php          # Proteccion CSRF
├── models/
│   ├── LogModel.php      # Modelo de bitacora
│   ├── ProductoModel.php # Modelo de productos
│   └── UsuarioModel.php  # Modelo de usuarios
├── views/
│   ├── img/
│   │   └── productos/        # Imagenes de productos
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
├── .htaccess             # Rutas amigables
├── database.sql          # Esquema de base de datos
├── index.php             # Punto de entrada (Front Controller)
└── README.md
```

## Validaciones incluidas

- Precio de venta debe ser mayor o igual al precio de compra
- Existencia debe ser mayor o igual a 0
- No se permiten valores negativos en precios
- SKU unico (no duplicados)
- Todos los campos obligatorios
- Tipos de datos numericos validados
- Archivos de imagen con formato y tamaño permitido
