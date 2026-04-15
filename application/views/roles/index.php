<div class="row">
    <div class="col-md-12">
        <div style="margin-bottom: 16px;">
            <a href="<?= base_url('roles/add') ?>" class="btn btn-primary">
                <i class="bx bx-plus"></i> Nuevo Rol
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tabla_roles">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Rol</th>
                        <th>Módulos Asignados</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><strong><?= htmlspecialchars($r['grupo']) ?></strong></td>
                        <td>
                            <span class="badge" style="background:#17a2b8;color:#fff;padding:4px 8px;border-radius:10px;">
                                <?= $r['total_modulos'] ?> módulos
                            </span>
                        </td>
                        <td>
                            <?php if ($r['activo'] == '1'): ?>
                                <span class="label label-success">Activo</span>
                            <?php else: ?>
                                <span class="label label-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('roles/edit/' . $r['id']) ?>" class="btn btn-xs btn-warning">
                                <i class="glyphicon glyphicon-edit"></i> Editar
                            </a>
                            <button onclick="eliminarRol(<?= $r['id'] ?>, '<?= htmlspecialchars($r['grupo']) ?>')"
                                class="btn btn-xs btn-danger">
                                <i class="glyphicon glyphicon-remove"></i> Eliminar
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tabla_roles').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json' }
    });
});

function eliminarRol(id, nombre) {
    Swal.fire({
        title: '¿Eliminar rol?',
        text: 'Se eliminará el rol "' + nombre + '"',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.get('<?= base_url('roles/eliminar') ?>?id=' + id, function(res) {
                if (res == '1') {
                    Swal.fire('Eliminado', 'El rol fue eliminado.', 'success')
                        .then(function() { location.reload(); });
                } else {
                    Swal.fire('No se puede eliminar', 'El rol tiene usuarios asignados. Reasigna los usuarios antes de eliminar.', 'error');
                }
            });
        }
    });
}
</script>
