<?php
/**
 * Registra el autoloader de clases del proyecto.
 *
 * Convierte el namespace en una ruta de archivo y lo carga automaticamente.
 *
 * @package Config
 * @author Tienda MVC
 * @version 1.0.0
 */
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/../';
    $class = str_replace('\\', '/', $class);
    $parts = explode('/', $class);
    if (!empty($parts)) {
        $parts[0] = strtolower($parts[0]);
    }
    $file = $baseDir . implode('/', $parts) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
