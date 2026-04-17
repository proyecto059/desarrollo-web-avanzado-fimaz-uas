<?php
/***********
 * CONFIGURACIÓN
 ***********/
$host = "localhost";
$db   = "escuela";
$user = "root";
$pass = "";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

/***********
 * CONEXIÓN PDO (con excepciones)
 ***********/
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

/***********
 * MENSAJES
 ***********/
$mensaje = "";
$detalle = "";

/***********
 * PROCESAR FORMULARIO
 ***********/
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre   = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $correo   = trim($_POST["correo"] ?? "");

    $simularError = isset($_POST["simular_error"]);

    if ($nombre === "" || $apellido === "" || $correo === "") {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    } else {

        try {
            // 1) Iniciar transacción
            $pdo->beginTransaction();

            // 2) Insertar alumno
            $sqlAlumno = "INSERT INTO alumnos (nombre, apellido, correo)
                          VALUES (:nombre, :apellido, :correo)";
            $stmtAlumno = $pdo->prepare($sqlAlumno);
            $stmtAlumno->execute([
                "nombre" => $nombre,
                "apellido" => $apellido,
                "correo" => $correo
            ]);

            $idAlumno = (int)$pdo->lastInsertId();

            // 3) Insertar log
            if ($simularError) {
                throw new Exception("Simulación de error activada: se fuerza rollback.");
            } else {
                $sqlLog = "INSERT INTO logs_alumnos (idAlumno, accion)
                           VALUES (:idAlumno, :accion)";
                $stmtLog = $pdo->prepare($sqlLog);
                $stmtLog->execute([
                    "idAlumno" => $idAlumno,
                    "accion" => "ALTA_ALUMNO"
                ]);
            }

            // 4) Confirmar transacción
            $pdo->commit();
            $mensaje = "✅ Transacción confirmada (COMMIT). Alumno registrado con ID: $idAlumno";

        } catch (Exception $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $mensaje = "❌ Ocurrió un error. Transacción revertida (ROLLBACK).";
            $detalle = $e->getMessage();
        }
    }
}

/***********
 * CONSULTAS
 ***********/
$alumnos = $pdo->query("SELECT * FROM alumnos ORDER BY idAlumno DESC")->fetchAll();
$logs    = $pdo->query("SELECT * FROM logs_alumnos ORDER BY idLog DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Práctica PDO</title>

<style>
body {font-family: Arial; margin:20px;}
.card {border:1px solid #ddd; padding:15px; margin-bottom:15px;}
label {display:block; margin-top:10px;}
input {padding:5px; width:100%;}
button {margin-top:10px; padding:8px;}
table {border-collapse: collapse; width:100%; margin-top:10px;}
th, td {border:1px solid #ccc; padding:5px;}
.small {font-size:12px;}
</style>

</head>
<body>

<h2>Práctica: try/catch y transacciones</h2>

<div class="card">
<form method="POST">

<label>Nombre</label>
<input type="text" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">

<label>Apellido</label>
<input type="text" name="apellido" value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>">

<label>Correo</label>
<input type="email" name="correo" value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">

<br><br>
<label>
<input type="checkbox" name="simular_error" <?= isset($_POST['simular_error']) ? 'checked' : '' ?>>
 Simular error
</label>

<button type="submit">Registrar alumno</button>

</form>
</div>

<?php if ($mensaje): ?>
<p><?= htmlspecialchars($mensaje) ?></p>
<?php if ($detalle): ?>
<p class="small"><?= htmlspecialchars($detalle) ?></p>
<?php endif; ?>
<?php endif; ?>

<div class="card">
<h3>Tabla alumnos</h3>

<?php if (!$alumnos): ?>
<p>Sin registros.</p>
<?php else: ?>
<table>
<tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>Correo</th></tr>
<?php foreach ($alumnos as $a): ?>
<tr>
<td><?= htmlspecialchars($a['idAlumno']) ?></td>
<td><?= htmlspecialchars($a['nombre']) ?></td>
<td><?= htmlspecialchars($a['apellido']) ?></td>
<td><?= htmlspecialchars($a['correo']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
</div>

<div class="card">
<h3>Logs</h3>

<?php if (!$logs): ?>
<p>Sin registros.</p>
<?php else: ?>
<table>
<tr><th>ID Log</th><th>ID Alumno</th><th>Acción</th><th>Fecha</th></tr>
<?php foreach ($logs as $l): ?>
<tr>
<td><?= htmlspecialchars($l['idLog']) ?></td>
<td><?= htmlspecialchars($l['idAlumno']) ?></td>
<td><?= htmlspecialchars($l['accion']) ?></td>
<td><?= htmlspecialchars($l['fecha']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
</div>

</body>
</html>