<?php

spl_autoload_register(function ($clase) {
    $clase = str_replace('Practica3\\', '/', $clase);
    $ruta = __DIR__ . '/clases/' . $clase . '.php';

    if (file_exists($ruta)) {
        require $ruta;
    }

});

use Practica3\Admin;
Use Practica3\Alumno;

try {

    $admin = new Admin("Alexis Ortiz", "rgzr");

    echo "nombre: " . $admin->getNombre();
    echo "correo: " . $admin->getCorreo();
    echo "Rol: " . $admin->getRol();


} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}