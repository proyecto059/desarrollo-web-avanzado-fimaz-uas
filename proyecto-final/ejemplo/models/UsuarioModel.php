<?php
namespace Models;

use Config\Database;
use PDO;
use PDOException;

/**
 * Clase que gestiona los usuarios administradores.
 *
 * @package Models
 * @author Tienda MVC
 * @version 1.0.0
 */
class UsuarioModel
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
     * Busca un usuario por su nombre de usuario.
     *
     * @param  string     $username Nombre de usuario a buscar
     * @return array|null           Datos del usuario si existe, null en caso contrario
     */
    public function buscarPorUsername(string $username): ?array
    {
        try {
            $sql = 'SELECT * FROM usuarios WHERE username = :username LIMIT 1';
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $usuario = $stmt->fetch();
            return $usuario ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }
}
