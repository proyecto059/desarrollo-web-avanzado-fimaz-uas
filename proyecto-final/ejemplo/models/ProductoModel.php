<?php
namespace Models;

use Config\Database;
use PDO;
use PDOException;

/**
 * Clase que gestiona los productos en la base de datos.
 *
 * @package Models
 * @author Tienda MVC
 * @version 1.0.0
 */
class ProductoModel
{
    /**
     * Conexion a la base de datos.
     *
     * @var PDO
     */
    private PDO $conexion;

    public function __construct()
    {
        $db = new Database();
        $this->conexion = $db->connect();
    }

    /**
     * Obtiene todos los productos paginados.
     *
     * @param  int   $page    Numero de pagina actual
     * @param  int   $perPage Cantidad de productos por pagina
     * @return array          Lista de productos
     */
    public function obtenerTodos(int $page = 1, int $perPage = 10): array
    {
        try {
            $offset = ($page - 1) * $perPage;
            $sql = 'SELECT * FROM productos ORDER BY id DESC LIMIT :limit OFFSET :offset';
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Cuenta el total de productos registrados.
     *
     * @return int Total de productos
     */
    public function contarTodos(): int
    {
        try {
            $sql = 'SELECT COUNT(*) as total FROM productos';
            $stmt = $this->conexion->query($sql);
            return (int)$stmt->fetch()['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Busca productos por nombre o descripcion con paginacion.
     *
     * @param  string $termino Termino de busqueda
     * @param  int    $page    Numero de pagina actual
     * @param  int    $perPage Resultados por pagina
     * @return array           Lista de productos coincidentes
     */
    public function buscarProducto(string $termino = '', int $page = 1, int $perPage = 10): array
    {
        try {
            $offset = ($page - 1) * $perPage;

            if (trim($termino) === '') {
                return $this->obtenerTodos($page, $perPage);
            }

            $sql = 'SELECT * FROM productos WHERE nombre LIKE :termino
            OR descripcion LIKE :termino ORDER BY id DESC LIMIT :limit OFFSET :offset';
            $stmt = $this->conexion->prepare($sql);
            $busqueda = '%' . $termino . '%';
            $stmt->bindParam(':termino', $busqueda);
            $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Cuenta los resultados de una busqueda.
     *
     * @param  string $termino Termino de busqueda
     * @return int             Total de coincidencias encontradas
     */
    public function contarBusqueda(string $termino = ''): int
    {
        try {
            if (trim($termino) === '') {
                return $this->contarTodos();
            }
            $sql = 'SELECT COUNT(*) as total FROM productos WHERE nombre LIKE :termino OR descripcion LIKE :termino';
            $stmt = $this->conexion->prepare($sql);
            $busqueda = '%' . $termino . '%';
            $stmt->bindParam(':termino', $busqueda);
            $stmt->execute();
            return (int)$stmt->fetch()['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Obtiene un producto por su ID.
     *
     * @param  int        $id ID del producto
     * @return array|null     Datos del producto si existe, null en caso contrario
     */
    public function obtenerPorId(int $id): ?array
    {
        try {
            $sql = 'SELECT * FROM productos WHERE id = :id LIMIT 1';
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $producto = $stmt->fetch();
            return $producto ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Verifica si un SKU ya existe en la base de datos.
     *
     * @param  string   $sku       SKU a verificar
     * @param  int|null $excludeId ID a excluir de la verificacion (para actualizaciones)
     * @return bool                True si el SKU ya existe, false en caso contrario
     */
    public function existeSku(string $sku, ?int $excludeId = null): bool
    {
        try {
            $sql = 'SELECT COUNT(*) as total FROM productos WHERE sku = :sku';
            if ($excludeId !== null) {
                $sql .= ' AND id != :id';
            }
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':sku', $sku);
            if ($excludeId !== null) {
                $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
            }
            $stmt->execute();
            return (int)$stmt->fetch()['total'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Crea un nuevo producto en la base de datos.
     *
     * @param  array $data Datos del producto (sku, nombre, descripcion, precios, existencia, imagen)
     * @return bool        True si se creo correctamente, false en caso contrario
     */
    public function crear(array $data): bool
    {
        try {
            $this->conexion->beginTransaction();

            $sql = 'INSERT INTO productos (sku, nombre, descripcion, precio_compra, precio_venta, existencia, imagen)
            VALUES (:sku, :nombre, :descripcion, :precio_compra, :precio_venta, :existencia, :imagen)';
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':sku', $data['sku']);
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':descripcion', $data['descripcion']);
            $stmt->bindParam(':precio_compra', $data['precio_compra']);
            $stmt->bindParam(':precio_venta', $data['precio_venta']);
            $stmt->bindParam(':existencia', $data['existencia'], PDO::PARAM_INT);
            $stmt->bindParam(':imagen', $data['imagen']);

            $resultado = $stmt->execute();
            if (!$resultado) {
                $this->conexion->rollBack();
                return false;
            }

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

    /**
     * Actualiza un producto existente.
     *
     * @param  int   $id   ID del producto a actualizar
     * @param  array $data Datos actualizados del producto
     * @return bool        True si se actualizo correctamente, false en caso contrario
     */
    public function actualizar(int $id, array $data): bool
    {
        try {
            $this->conexion->beginTransaction();

            $sql = 'UPDATE productos SET
                        sku = :sku,
                        nombre = :nombre,
                        descripcion = :descripcion,
                        precio_compra = :precio_compra,
                        precio_venta = :precio_venta,
                        existencia = :existencia,
                        imagen = :imagen
                    WHERE id = :id';

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':sku', $data['sku']);
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':descripcion', $data['descripcion']);
            $stmt->bindParam(':precio_compra', $data['precio_compra']);
            $stmt->bindParam(':precio_venta', $data['precio_venta']);
            $stmt->bindParam(':existencia', $data['existencia'], PDO::PARAM_INT);
            $stmt->bindParam(':imagen', $data['imagen']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

    /**
     * Elimina un producto por su ID.
     *
     * @param  int  $id ID del producto a eliminar
     * @return bool     True si se elimino correctamente, false en caso contrario
     */
    public function eliminar(int $id): bool
    {
        try {
            $this->conexion->beginTransaction();
            $sql = 'DELETE FROM productos WHERE id = :id';
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                $this->conexion->rollBack();
                return false;
            }

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }
}
