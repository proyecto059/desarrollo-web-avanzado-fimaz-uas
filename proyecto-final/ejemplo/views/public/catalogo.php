<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2>Catalogo publico de productos</h2>
        <p>Consulta los productos disponibles y realiza busqueda por nombre o descripcion</p>
    </div>
</div>

<form method="GET" action="<?= BASE_URL ?>catalogo" class="row g-2 mb-4">
    <div class="col-md-10">
        <input type="text" name="buscar" class="form-control"
        placeholder="Buscar por nombre o descripcion"
        value="<?= htmlspecialchars($termino ?? '') ?>">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Buscar</button>
    </div>
</form>

<div class="row">
    <?php if (!empty($productos)): ?>
        <?php foreach ($productos as $producto): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($producto['imagen'])): ?>
                        <img src="<?= BASE_URL ?>views/img/productos/<?= htmlspecialchars($producto['imagen']); ?>"
                             class="img-card-catalog" alt="<?= htmlspecialchars($producto['nombre']); ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($producto['nombre']); ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">SKU: <?= htmlspecialchars($producto['sku']); ?></h6>
                        <p class="card-text"><?= htmlspecialchars($producto['descripcion']); ?></p>
                        <p><strong>Precio:</strong> $<?= number_format((float)$producto['precio_venta'], 2); ?></p>
                        <p><strong>Existencia:</strong> <?= number_format((int)$producto['existencia']); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-warning">No se encontraron productos</div>
        </div>
    <?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
<nav>
    <ul class="pagination justify-content-center">
        <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?= BASE_URL ?>catalogo?buscar=<?= urlencode($termino); ?>&page=<?= $page - 1; ?>">Anterior</a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="<?= BASE_URL ?>catalogo?buscar=<?= urlencode($termino); ?>&page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?= BASE_URL ?>catalogo?buscar=<?= urlencode($termino); ?>&page=<?= $page + 1; ?>">Siguiente</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
