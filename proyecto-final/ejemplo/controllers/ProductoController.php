<?php
namespace Controllers;

use Models\ProductoModel;
use Models\LogModel;
use Helpers\Csrf;

/**
 * Clase que gestiona la administracion de productos.
 *
 * @package Controllers
 * @author Tienda MVC
 * @version 1.0.0
 */
class ProductoController
{
    /**
     * Instancia del modelo de productos.
     *
     * @var ProductoModel
     */
    private ProductoModel $productoModel;

    public function __construct()
    {
        $this->productoModel = new ProductoModel();
    }

    /**
     * Verifica si hay una sesion de administrador activa.
     *
     * Redirige al login si no existe una sesion valida.
     */
    private function verificarSesion(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['admin'])) {
            header('Location: login');
            exit;
        }
    }

    /**
     * Valida el token CSRF y redirige si es invalido.
     */
    private function redirigirSiNoCsrf(): void
    {
        if (!Csrf::validar($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de seguridad invalido.';
            header('Location: productos');
            exit;
        }
    }

    /**
     * Registra una accion en la bitacora del administrador.
     *
     * @param string      $accion   Nombre de la accion realizada
     * @param string|null $detalles Detalles adicionales de la accion
     */
    private function registrarLog(string $accion, ?string $detalles = null): void
    {
        $log = new LogModel();
        $log->registrar(
            $_SESSION['admin']['id'],
            $_SESSION['admin']['username'],
            $accion,
            $detalles
        );
    }

    /**
     * Procesa la subida de una imagen de producto.
     *
     * Valida el formato y tamano del archivo. Elimina la imagen anterior
     * si existe y se esta reemplazando.
     *
     * @param  array|null  $file         Archivo subido desde $_FILES['imagen']
     * @param  string|null $imagenActual Nombre de la imagen actual del producto
     * @return string                    Nombre del archivo guardado o cadena vacia
     */
    private function procesarImagen(?array $file, ?string $imagenActual = null): string
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return $imagenActual ?? '';
        }

        $dirUploads = __DIR__ . '/../views/img/productos/';
        if (!is_dir($dirUploads)) {
            mkdir($dirUploads, 0775, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extension, $permitidas)) {
            $_SESSION['error'] = 'Tipo de imagen no permitido. Use: jpg, jpeg, png, gif, webp.';
            return $imagenActual ?? '';
        }

        $maxSize = 2 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = 'La imagen no debe superar los 2MB.';
            return $imagenActual ?? '';
        }

        if ($imagenActual && file_exists($dirUploads . $imagenActual)) {
            unlink($dirUploads . $imagenActual);
        }

        $nombreArchivo = uniqid('img_') . '.' . $extension;
        if (move_uploaded_file($file['tmp_name'], $dirUploads . $nombreArchivo)) {
            return $nombreArchivo;
        }

        $_SESSION['error'] = 'Error al subir la imagen.';
        return $imagenActual ?? '';
    }

    /**
     * Muestra el listado paginado de productos.
     */
    public function index(): void
    {
        $this->verificarSesion();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        $productos = $this->productoModel->obtenerTodos($page, $perPage);
        $total = $this->productoModel->contarTodos();
        $totalPages = max(1, (int)ceil($total / $perPage));

        require_once __DIR__ . '/../views/productos/index.php';
    }

    /**
     * Muestra el formulario de creacion de producto.
     */
    public function create(): void
    {
        $this->verificarSesion();
        require_once __DIR__ . '/../views/productos/create.php';
    }

    /**
     * Procesa el formulario de creacion de producto.
     *
     * Valida campos obligatorios, tipos numericos, valores negativos,
     * relacion precio venta >= precio compra, SKU unico, CSRF e imagen.
     */
    public function store(): void
    {
        $this->verificarSesion();
        $this->redirigirSiNoCsrf();

        $data = [
            'sku' => trim($_POST['sku'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'precio_compra' => trim($_POST['precio_compra'] ?? ''),
            'precio_venta' => trim($_POST['precio_venta'] ?? ''),
            'existencia' => trim($_POST['existencia'] ?? ''),
            'imagen' => ''
        ];

        if (
            $data['sku'] === '' ||
            $data['nombre'] === '' ||
            $data['descripcion'] === '' ||
            $data['precio_compra'] === '' ||
            $data['precio_venta'] === '' ||
            $data['existencia'] === ''
        ) {
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header('Location: productos/create');
            exit;
        }

        if (!is_numeric($data['precio_compra']) || !is_numeric($data['precio_venta'])
            || !is_numeric($data['existencia'])) {
            $_SESSION['error'] = 'Precio de compra, precio de venta y existencia deben ser numericos.';
            header('Location: productos/create');
            exit;
        }

        $precioCompra = (float)$data['precio_compra'];
        $precioVenta = (float)$data['precio_venta'];
        $existencia = (int)$data['existencia'];

        if ($precioCompra < 0 || $precioVenta < 0) {
            $_SESSION['error'] = 'No se permiten valores negativos en los precios.';
            header('Location: productos/create');
            exit;
        }

        if ($existencia < 0) {
            $_SESSION['error'] = 'La existencia debe ser mayor o igual que 0.';
            header('Location: productos/create');
            exit;
        }

        if ($precioVenta < $precioCompra) {
            $_SESSION['error'] = 'El precio de venta debe ser mayor o igual que el precio de compra.';
            header('Location: productos/create');
            exit;
        }

        if ($this->productoModel->existeSku($data['sku'])) {
            $_SESSION['error'] = 'El SKU ingresado ya existe. Debe usar un SKU unico.';
            header('Location: productos/create');
            exit;
        }

        $data['imagen'] = $this->procesarImagen($_FILES['imagen'] ?? null);

        if (!empty($_SESSION['error'])) {
            header('Location: productos/create');
            exit;
        }

        if ($this->productoModel->crear($data)) {
            $_SESSION['success'] = 'Producto registrado correctamente.';
            $this->registrarLog('Crear producto', "SKU: {$data['sku']}, Nombre: {$data['nombre']}");
        } else {
            $_SESSION['error'] = 'No fue posible registrar el producto.';
        }

        header('Location: productos');
        exit;
    }

    /**
     * Muestra el formulario de edicion de producto.
     *
     * @param int $id ID del producto a editar
     */
    public function edit(): void
    {
        $this->verificarSesion();

        $id = (int)($_GET['id'] ?? 0);
        $producto = $this->productoModel->obtenerPorId($id);

        if (!$producto) {
            $_SESSION['error'] = 'Producto no encontrado.';
            header('Location: productos');
            exit;
        }

        require_once __DIR__ . '/../views/productos/edit.php';
    }

    /**
     * Procesa el formulario de actualizacion de producto.
     *
     * Valida campos obligatorios, tipos numericos, valores negativos,
     * relacion precio venta >= precio compra, SKU unico (excluyendo el actual),
     * CSRF e imagen.
     */
    public function update(): void
    {
        $this->verificarSesion();
        $this->redirigirSiNoCsrf();

        $id = (int)($_POST['id'] ?? 0);

        $data = [
            'sku' => trim($_POST['sku'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'precio_compra' => trim($_POST['precio_compra'] ?? ''),
            'precio_venta' => trim($_POST['precio_venta'] ?? ''),
            'existencia' => trim($_POST['existencia'] ?? ''),
            'imagen' => ''
        ];

        if ($id <= 0) {
            $_SESSION['error'] = 'ID invalido.';
            header('Location: productos');
            exit;
        }

        $productoActual = $this->productoModel->obtenerPorId($id);
        if (!$productoActual) {
            $_SESSION['error'] = 'Producto no encontrado.';
            header('Location: productos');
            exit;
        }

        if (
            $data['sku'] === '' ||
            $data['nombre'] === '' ||
            $data['descripcion'] === '' ||
            $data['precio_compra'] === '' ||
            $data['precio_venta'] === '' ||
            $data['existencia'] === ''
        ) {
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header("Location: productos/edit?id=$id");
            exit;
        }

        if (!is_numeric($data['precio_compra']) || !is_numeric($data['precio_venta'])
            || !is_numeric($data['existencia'])) {
            $_SESSION['error'] = 'Precio de compra, precio de venta y existencia deben ser numericos.';
            header("Location: productos/edit?id=$id");
            exit;
        }

        $precioCompra = (float)$data['precio_compra'];
        $precioVenta = (float)$data['precio_venta'];
        $existencia = (int)$data['existencia'];

        if ($precioCompra < 0 || $precioVenta < 0) {
            $_SESSION['error'] = 'No se permiten valores negativos en los precios.';
            header("Location: productos/edit?id=$id");
            exit;
        }

        if ($existencia < 0) {
            $_SESSION['error'] = 'La existencia debe ser mayor o igual que 0.';
            header("Location: productos/edit?id=$id");
            exit;
        }

        if ($precioVenta < $precioCompra) {
            $_SESSION['error'] = 'El precio de venta debe ser mayor o igual que el precio de compra.';
            header("Location: productos/edit?id=$id");
            exit;
        }

        if ($this->productoModel->existeSku($data['sku'], $id)) {
            $_SESSION['error'] = 'El SKU ingresado ya existe. Debe usar un SKU unico.';
            header("Location: productos/edit?id=$id");
            exit;
        }

        $data['imagen'] = $this->procesarImagen(
            $_FILES['imagen'] ?? null,
            $productoActual['imagen'] ?? ''
        );

        if (!empty($_SESSION['error'])) {
            header("Location: productos/edit?id=$id");
            exit;
        }

        if ($this->productoModel->actualizar($id, $data)) {
            $_SESSION['success'] = 'Producto actualizado correctamente.';
            $this->registrarLog('Actualizar producto', "ID: $id, SKU: {$data['sku']}, Nombre: {$data['nombre']}");
        } else {
            $_SESSION['error'] = 'No fue posible actualizar el producto.';
        }

        header('Location: productos');
        exit;
    }

    /**
     * Elimina un producto y su imagen asociada.
     */
    public function delete(): void
    {
        $this->verificarSesion();
        $this->redirigirSiNoCsrf();

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error'] = 'ID invalido.';
            header('Location: productos');
            exit;
        }

        $producto = $this->productoModel->obtenerPorId($id);
        if (!$producto) {
            $_SESSION['error'] = 'Producto no encontrado.';
            header('Location: productos');
            exit;
        }

        if ($producto['imagen'] && file_exists(__DIR__ . '/../views/img/productos/' . $producto['imagen'])) {
            unlink(__DIR__ . '/../views/img/productos/' . $producto['imagen']);
        }

        if ($this->productoModel->eliminar($id)) {
            $_SESSION['success'] = 'Producto eliminado correctamente.';
            $this->registrarLog('Eliminar producto', "ID: $id, SKU: {$producto['sku']}, Nombre: {$producto['nombre']}");
        } else {
            $_SESSION['error'] = 'No fue posible eliminar el producto.';
        }

        header('Location: productos');
        exit;
    }

    /**
     * Muestra la bitacora de actividades del administrador.
     */
    public function bitacora(): void
    {
        $this->verificarSesion();

        $logModel = new LogModel();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $logs = $logModel->obtenerTodos($page, $perPage);
        $total = $logModel->contarTodos();
        $totalPages = max(1, (int)ceil($total / $perPage));

        require_once __DIR__ . '/../views/productos/bitacora.php';
    }
}
