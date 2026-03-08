<?php

spl_autoload_register(function ($clase) {

    $clase = str_replace('Practica1\\', '', $clase);
    $ruta = __DIR__ . '/' . $clase . '.php';

    if (file_exists($ruta)) {
        require $ruta;
    }

});

use Practica1\Usuario;

$usuario = new Usuario("Alexis Ortiz", "ortizcristhian503@gmail.com");

echo "Datos del usuario:<br>";
echo "Nombre: " . $usuario->getNombre() . "<br>";
echo "Correo: " . $usuario->getCorreo();

?>