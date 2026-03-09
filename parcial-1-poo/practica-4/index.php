<?php

spl_autoload_register(function ($clase) {

    $clase = str_replace('Clases\\', '', $clase);
    $ruta = __DIR__ . '/clases/' . $clase . '.php';

    if (file_exists($ruta)) {
        require $ruta;
    }

});

use Clases\Admin;
use Clases\Alumno;
use Clases\Invitado;

$usuarios = [];

try {

    $admin = new Admin("Alexis Ortiz", "ortizcristhian503@gmail.com");
    $usuarios[] = $admin;

    $alumno = new Alumno("pedro", "carlos@email.com", "A12345");
    $usuarios[] = $alumno;

    $invitado = new Invitado("Pablo", "pablo@email.com", "google");
    $usuarios[] = $invitado;

    $error = new Admin("Pedro Ramirez", "rgzr");
    $usuarios[] = $error;

} catch (Exception $e) {

    echo "<p style='color:red;'>Error controlado: " . $e->getMessage() . "</p>";

}

?>

<h2>Lista de Usuarios</h2>

<table border="1" cellpadding="8">

<tr>
<th>Nombre</th>
<th>Correo</th>
<th>Rol</th>
<th>Matrícula</th>
<th>Empresa</th>
</tr>

<?php foreach ($usuarios as $u) { ?>

<tr>

<td><?php echo $u->getNombre(); ?></td>
<td><?php echo $u->getCorreo(); ?></td>
<td><?php echo $u->getRol(); ?></td>

<td>
<?php
if ($u instanceof Alumno) {
    echo $u->getMatricula();
} else {
    echo "—";
}
?>
</td>

<td>
<?php
if ($u instanceof Invitado) {
    echo $u->getEmpresa();
} else {
    echo "—";
}
?>
</td>

</tr>

<?php } ?>

</table>