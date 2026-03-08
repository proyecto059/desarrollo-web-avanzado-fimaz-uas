<?php

namespace Practica2;

use Practica2\Usuario;

class Admin extends Usuario {

    public function getRol(){
        return "Administrador";
    }
}