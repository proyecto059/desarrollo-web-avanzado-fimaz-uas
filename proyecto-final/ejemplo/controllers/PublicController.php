<?php
namespace Controllers;

use Models\ProductoModel;

/**
 * Clase que gestiona las vistas publicas del catalogo.
 *
 * @package Controllers
 * @author Tienda MVC
 * @version 1.0.0
 */
class PublicController
{
    /**
     * Muestra el catalogo publico con busqueda y paginacion.
     */
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
}
