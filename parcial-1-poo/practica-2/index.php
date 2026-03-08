<?php

spl_autoload_register(function ($clase) {

    $clase = str_replace('Practica2\\', '', $clase);
    $ruta = __DIR__ . '/' . $clase . '.php';

    if (file_exists($ruta)) {
        require $ruta;
    }
});

use Practica2\Admin;

$admin = new Admin("Alexis Ortiz", "ortizcristhian503@gmail.com");

echo "Nombre: " . $admin->getNombre();
echo "Correo:" . $admin->getCorreo();
echo "Rol: " . $admin->getRol();

