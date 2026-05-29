<?php
//Cristhian Alexis Ortiz Valentin
class Database{
    private $host = "localhost";
    private $db_name = "producto";
    private $username = "root";
    private $password = "";
    private $conn;


    public function getConnection(){
        $this->conn = null;

        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception){
            echo "Error en conexion a la base de datos: " .  $exception->getMessage();
        }

        return $this->conn;
    }
}

?>