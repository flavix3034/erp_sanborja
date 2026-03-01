<?php if(isset($msg)): ?>
<div class="alert alert-<?= isset($rpta_msg) ? $rpta_msg : 'success' ?>"><?= $msg ?></div>
<?php endif; ?>

<div class="row filitas">
	<div class="col-sm-12">
		<a href="<?= base_url('empleados/add') ?>" class="btn btn-success btn-sm" style="margin-bottom:10px;">
			<i class="fa fa-plus"></i> Agregar Empleado
		</a>
		<div class="panel panel-default">
			<div class="panel-heading"><h4><i class="fa fa-users"></i> Listado de Empleados</h4></div>
			<div class="panel-body">
				<div class="table-responsive">
					<table id="tabla_empleados" class="table table-striped table-bordered" style="width:100%">
						<thead>
							<tr style="background-color:#f7f7f7;">
								<th style="width:40px;">#</th>
								<th>Apellidos</th>
								<th>Nombres</th>
								<th>DNI</th>
								<th>Cargo</th>
								<th>Area</th>
								<th>Teléfono</th>
								<th>Ingreso</th>
								<th style="width:80px;">Estado</th>
								<th style="width:120px;">Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php $n = 0; foreach($empleados as $e): $n++; ?>
							<tr>
								<td class="text-center"><?= $n ?></td>
								<td><?= htmlspecialchars($e['apellidos']) ?></td>
								<td><?= htmlspecialchars($e['nombres']) ?></td>
								<td><?= htmlspecialchars($e['dni']) ?></td>
								<td><?= htmlspecialchars($e['cargo']) ?></td>
								<td><?= htmlspecialchars($e['area']) ?></td>
								<td><?= htmlspecialchars($e['telefono']) ?></td>
								<td><?= !empty($e['fecha_ingreso']) ? date('d/m/Y', strtotime($e['fecha_ingreso'])) : '' ?></td>
								<td class="text-center">
									<?php if($e['activo'] == '1'): ?>
									<span class="badge" style="background-color:#28a745;color:#fff;">Activo</span>
									<?php else: ?>
									<span class="badge" style="background-color:#999;color:#fff;">Inactivo</span>
									<?php endif; ?>
								</td>
								<td class="text-center">
									<a href="<?= base_url('empleados/edit/' . $e['id']) ?>" class="btn btn-xs btn-info" title="Editar"><i class="fa fa-edit"></i></a>
									<?php if($e['activo'] == '1'): ?>
									<a href="<?= base_url('empleados/anular/' . $e['id']) ?>" class="btn btn-xs btn-danger" title="Desactivar" onclick="return confirm('¿Desactivar este empleado?');"><i class="fa fa-ban"></i></a>
									<?php else: ?>
									<a href="<?= base_url('empleados/activar/' . $e['id']) ?>" class="btn btn-xs btn-success" title="Activar" onclick="return confirm('¿Activar este empleado?');"><i class="fa fa-check"></i></a>
									<?php endif; ?>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	$('#tabla_empleados').DataTable({
		"order": [[ 1, "asc" ]],
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
		},
		"pageLength": 25,
		"dom": 'frtip'
	});
});
</script>

<style>
.filitas { margin-bottom: 15px; }
.panel-heading h4 { margin: 0; font-size: 15px; font-weight: bold; }
#tabla_empleados th, #tabla_empleados td {
	font-size: 12px;
	vertical-align: middle;
}
#tabla_empleados thead th {
	font-weight: bold;
}
</style>
