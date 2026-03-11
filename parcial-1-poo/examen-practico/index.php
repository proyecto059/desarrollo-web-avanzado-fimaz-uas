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

$usuarios = [];

try {

    $admin = new Admin("Alexis Ortiz", "Ortizcristhian503@gmail.com");
    $usuarios[] = $admin;

    $alumno = new Alumno("Cristhian Valentin", "Ortizcristhian504@gmail.com", "20888538");
    $usuarios[] = $alumno;

    // Usuario con correo inválido
    $alumno = new Alumno("Ortiz Valentin", "loquesea", "20888539");
    $usuarios[] = $alumno;

} catch (Exception $e){

    echo "<p>Error controlado: " . $e->getMessage() . "</p>";

}

?>

<h2>Lista de Usuarios</h2>

<table border="1" cellpadding="8">

<tr>
<th>Nombre</th>
<th>Correo</th>
<th>Rol</th>
<th>Matrícula</th>
</tr>

<?php foreach($usuarios as $u){ ?>

<tr>

<td><?php echo $u->getNombre(); ?></td>
<td><?php echo $u->getCorreo(); ?></td>
<td><?php echo $u->getRol(); ?></td>

<td>
<?php
if($u instanceof Alumno){
    echo $u->getMatricula();
}else{
    echo "—";
}
?>
</td>

</tr>

<?php } ?>

</table>