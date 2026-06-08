<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php use Helpers\Csrf; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Administrador de productos</h2>
    <div>
        <a href="<?= BASE_URL ?>productos/create" class="btn btn-success">Nuevo producto</a>
        <a href="<?= BASE_URL ?>productos/bitacora" class="btn btn-info">Bitacora</a>
        <a href="<?= BASE_URL ?>logout" class="btn btn-danger">Cerrar sesion</a>
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>SKU</th>
            <th>Nombre</th>
            <th>Precio compra</th>
            <th>Precio venta</th>
            <th>Existencia</th>
            <th>Imagen</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($productos as $producto): ?>
            <tr>
                <td><?= (int)$producto['id']; ?></td>
                <td><?= htmlspecialchars($producto['sku']); ?></td>
                <td><?= htmlspecialchars($producto['nombre']); ?></td>
                <td><?= number_format((float)$producto['precio_compra'], 2); ?></td>
                <td><?= number_format((float)$producto['precio_venta'], 2); ?></td>
                <td><?= (int)$producto['existencia']; ?></td>
                <td>
                    <?php if (!empty($producto['imagen'])): ?>
                        <img class="img-thumb-admin" src="<?= BASE_URL ?>views/img/productos/<?= htmlspecialchars($producto['imagen']); ?>" alt="Imagen">
                    <?php else: ?>
                        <span class="text-muted">Sin imagen</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?= BASE_URL ?>productos/edit?id=<?= (int)$producto['id']; ?>"
                    class="btn btn-primary btn-sm">Editar</a>

                    <form action="<?= BASE_URL ?>productos/delete" method="POST" class="d-inline">
                        <?= Csrf::campo(); ?>
                        <input type="hidden" name="id" value="<?= (int)$producto['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Deseas eliminar este producto?');">
                        Eliminar
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if ($totalPages > 1): ?>
<nav>
    <ul class="pagination justify-content-center">
        <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?= BASE_URL ?>productos?page=<?= $page - 1; ?>">Anterior</a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="<?= BASE_URL ?>productos?page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?= BASE_URL ?>productos?page=<?= $page + 1; ?>">Siguiente</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
