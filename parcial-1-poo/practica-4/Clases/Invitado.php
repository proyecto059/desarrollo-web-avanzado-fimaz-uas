<?php

namespace Clases;

class Invitado extends Usuario {

    private $empresa;

    public function __construct($nombre, $correo, $empresa)
    {
        parent::__construct($nombre, $correo);
        $this->empresa = $empresa;
    }

    public function getEmpresa()
    {
        return $this->empresa;
    }

    public function getRol()
    {
        return "Invitado";
    }

}