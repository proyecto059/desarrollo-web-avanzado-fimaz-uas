<?php
//representaion de ka entidad de la base de datos
class  Producto{

    private $id;
    private $nombre;
    private $descripcion;
    private $precio;
    private $existencia;

    public function __construct($id=null, $nombre='', $descripcion='', $precio=0, $existencia=0){
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->existencia = $existencia;
    }

    public function setId($id){
        $this->id = $id;
    
    }
    public function setNombre($nombre){
        $this->nombre = $nombre;
    }

    public function setDescripcion($descripcion){
        $this->descripcion=$descripcion;
    }

    public function setPrecio($precio){
        $this->precio = $precio;
    }

    public function setExistencia($existencia){
        $this->existencia = $existencia;
    }

    public function getId(){
        return $this->id;
    
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function getDescripcion(){
        return $this ->descripcion;
    }   

    public function getPrecio(){
        return $this ->precio;
    }

    public function getExistencia(){
        return $this->existencia;
    }
}
   
?>