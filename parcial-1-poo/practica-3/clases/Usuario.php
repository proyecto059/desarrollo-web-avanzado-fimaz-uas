<?php

namespace Practica3;

Class Usuario {

    protected $nombre;
    protected $correo;

    public function __construct($nombre, $correo) {

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        throw new \Exception("Correo invalido: $correo");
    }

        $this -> nombre = $nombre;
        $this ->correo = $correo; 
    }
    public function getNombre(){
        return $this -> nombre;
    }

    public function getCorreo(){
        return $this -> correo;
    }
}