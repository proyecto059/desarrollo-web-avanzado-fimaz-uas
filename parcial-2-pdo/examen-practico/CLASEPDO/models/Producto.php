<?php

namespace models;

class Producto{
    private $id;
    private $nombre;
    private $descripcion;
    private $existencia;
    private $precio;

    public function __construct($id = null, $nombre = "", $descripcion = "", $existencia = 0, $precio = 0.00)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->existencia = $existencia;
        $this->precio = $precio;
    }

    public function __destruct()
    {

    }

    public function setId($id){
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }
    public function setNombre($nombre){
        $this->nombre = $nombre;
    }
    public function getNombre(){
        return $this->nombre;
    }

    public function setDescripcion($descripcion){
        $this->descripcion = $descripcion;
    }
    public function getDescripcion(){
        return $this->descripcion;
    }
    
    public function setExistencia($existencia){
        $this->existencia = $existencia;
    }
    public function getExistencia(){
        return $this->existencia;
    }

    public function setPrecio($precio){
        $this->precio = $precio;
    }
    public function getPrecio(){
        return $this->precio;
    }
}
    
?>