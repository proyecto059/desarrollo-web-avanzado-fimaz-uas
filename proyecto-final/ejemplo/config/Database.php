<?php
namespace Config;

use PDO;
use PDOException;

/**
 * Clase que gestiona la conexion a la base de datos MySQL.
 *
 * @package Config
 * @author Tienda MVC
 * @version 1.0.0
 */
class Database {
    /**
     * Host del servidor MySQL.
     *
     * @var string
     */
    private string $host = "localhost";

    /**
     * Nombre de la base de datos.
     *
     * @var string
     */
    private string $dbName = "tienda_mvc";

    /**
     * Usuario de conexion.
     *
     * @var string
     */
    private string $username = "root";

    /**
     * Contrasena del usuario.
     *
     * @var string
     */
    private string $password = "";

    /**
     * Juego de caracteres.
     *
     * @var string
     */
    private string $charset = "utf8mb4";

    /**
     * Establece y retorna la conexion PDO.
     */
    public function connect() : PDO
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset={$this->charset}";
            $pdo = new PDO($dsn, $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch (PDOException $e) {
            die('Error de conexion: ' . $e->getMessage());
        }
    }
}
