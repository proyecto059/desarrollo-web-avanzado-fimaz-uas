<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Bitacora de actividades</h2>
    <a href="<?= BASE_URL ?>productos" class="btn btn-secondary">Volver a productos</a>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Administrador</th>
            <th>Accion</th>
            <th>Detalles</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($logs)): ?>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= (int)$log['id']; ?></td>
                    <td><?= htmlspecialchars($log['admin_username']); ?></td>
                    <td><?= htmlspecialchars($log['accion']); ?></td>
                    <td><?= htmlspecialchars($log['detalles'] ?? ''); ?></td>
                    <td><?= htmlspecialchars($log['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No hay registros en la bitacora.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if ($totalPages > 1): ?>
<nav>
    <ul class="pagination justify-content-center">
        <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?= BASE_URL ?>productos/bitacora?page=<?= $page - 1; ?>">Anterior</a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="<?= BASE_URL ?>productos/bitacora?page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?= BASE_URL ?>productos/bitacora?page=<?= $page + 1; ?>">Siguiente</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
