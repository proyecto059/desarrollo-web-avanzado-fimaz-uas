<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Desarrollo Web Avanzado: POO+PDO+TryCatch-Namespace-Autoload-Transaccion-MVC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<style>
.img-thumb-admin{width:60px;height:60px;object-fit:cover;object-position:center;border-radius:4px;display:block;margin:0 auto}
.img-preview-edit{width:150px;height:150px;object-fit:contain;object-position:center;background:#f8f9fa;border-radius:4px;padding:4px;display:block}
.img-card-catalog{width:100%;height:200px;object-fit:cover;object-position:center;display:block}
html,body{height:100%}
</style>
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>catalogo">Tienda MVC</a>
        <div>
            <a class="btn btn-outline-light btn-sm me-2" href="<?= BASE_URL ?>catalogo">Catalogo</a>
            <?php if (isset($_SESSION['admin'])): ?>
                <a class="btn btn-info btn-sm me-2" href="<?= BASE_URL ?>productos">Admin</a>
                <a class="btn btn-danger btn-sm" href="<?= BASE_URL ?>logout">Cerrar sesion</a>
            <?php else: ?>
                <a class="btn btn-warning btn-sm" href="<?= BASE_URL ?>login">Administrador</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-4 flex-grow-1">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
