<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php use Helpers\Csrf; ?>

<h2>Editar producto</h2>

<form action="<?= BASE_URL ?>productos/update" method="POST" enctype="multipart/form-data">
    <?= Csrf::campo(); ?>
    <input type="hidden" name="id" value="<?= (int)$producto['id']; ?>">

    <div class="mb-3">
        <label class="form-label">SKU</label>
        <input type="text" name="sku" class="form-control" value="<?= htmlspecialchars($producto['sku']); ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($producto['nombre']); ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Descripcion</label>
        <textarea name="descripcion" class="form-control" required><?= htmlspecialchars($producto['descripcion']); ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Precio compra</label>
        <input type="number" step="0.01" name="precio_compra" class="form-control"
        value="<?= htmlspecialchars((string)$producto['precio_compra']); ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Precio venta</label>
        <input type="number" step="0.01" name="precio_venta" class="form-control"
        value="<?= htmlspecialchars((string)$producto['precio_venta']); ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Existencia</label>
        <input type="number" name="existencia" class="form-control" value="<?= (int)$producto['existencia']; ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Imagen del producto</label>
        <?php if (!empty($producto['imagen'])): ?>
            <div class="mb-2">
                <img class="img-preview-edit" src="<?= BASE_URL ?>views/img/productos/<?= htmlspecialchars($producto['imagen']); ?>" alt="Imagen actual">
                <p class="text-muted small">Imagen actual. Sube una nueva para reemplazarla.</p>
            </div>
        <?php endif; ?>
        <input type="file" name="imagen" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
    </div>

    <button type="submit" class="btn btn-success">Guardar cambios</button>
    <a href="<?= BASE_URL ?>productos" class="btn btn-secondary">Cancelar</a>
</form>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
