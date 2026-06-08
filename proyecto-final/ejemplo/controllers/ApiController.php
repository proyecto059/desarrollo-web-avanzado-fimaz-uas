<?php
namespace Controllers;

use Models\ProductoModel;

/**
 * Clase que gestiona la API REST de productos.
 *
 * @package Controllers
 * @author Tienda MVC
 * @version 1.0.0
 */
class ApiController
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
     * Envia una respuesta JSON al cliente.
     *
     * @param mixed $data   Datos a serializar como JSON
     * @param int   $status Codigo de estado HTTP de la respuesta
     */
    private function jsonResponse(mixed $data, int $status = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Retorna el listado completo de productos en formato JSON.
     */
    public function productos(): void
    {
        $productos = $this->productoModel->obtenerTodos();
        $this->jsonResponse(['success' => true, 'data' => $productos]);
    }

    /**
     * Retorna un producto especifico en formato JSON.
     *
     * @param int $id ID del producto a consultar
     */
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
}
