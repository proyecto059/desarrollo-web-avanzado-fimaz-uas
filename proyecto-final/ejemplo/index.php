<?php
/**
 * Punto de entrada principal del sistema (Front Controller).
 *
 * Inicia la sesion, carga las dependencias mediante el autoloader
 * y enruta la solicitud al controlador correspondiente segun el
 * parametro 'route' obtenido de la URL.
 *
 * @package null
 * @author Tienda MVC
 * @version 1.0.0
 */
require_once __DIR__ . '/config/Autoload.php';

use Controllers\AuthController;
use Controllers\ProductoController;
use Controllers\PublicController;
use Controllers\ApiController;
use Helpers\Csrf;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$baseDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
define('BASE_URL', $baseDir . '/');

$route = $_GET['route'] ?? 'catalogo';

$authController = new AuthController();
$productoController = new ProductoController();
$publicController = new PublicController();
$apiController = new ApiController();

if (preg_match('#^api/productos/(\d+)$#', $route, $matches)) {
    $apiController->productoPorId((int)$matches[1]);
}

switch ($route) {
    case 'login':
        $authController->showLogin();
        break;

    case 'auth/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->login();
        } else {
            $authController->showLogin();
        }
        break;

    case 'logout':
        $authController->logout();
        break;

    case 'productos':
        $productoController->index();
        break;

    case 'productos/create':
        $productoController->create();
        break;

    case 'productos/store':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productoController->store();
        } else {
            header('Location: ' . BASE_URL . 'productos');
            exit;
        }
        break;

    case 'productos/edit':
        $productoController->edit();
        break;

    case 'productos/update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productoController->update();
        } else {
            header('Location: ' . BASE_URL . 'productos');
            exit;
        }
        break;

    case 'productos/delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productoController->delete();
        } else {
            header('Location: ' . BASE_URL . 'productos');
            exit;
        }
        break;

    case 'productos/bitacora':
        $productoController->bitacora();
        break;

    case 'api/productos':
        $apiController->productos();
        break;

    case 'catalogo':
    default:
        $publicController->catalogo();
        break;
}
